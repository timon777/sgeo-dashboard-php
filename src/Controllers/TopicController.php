<?php

namespace App\Controllers;

use App\Models\Project;
use App\Models\AiResponse;
use App\Models\Source;
use App\Models\Evaluation;
use App\Services\SupabaseClient;
use App\Services\Cache;

class TopicController extends BaseController
{
    private SupabaseClient $db;

    public function __construct()
    {
        $this->db = new SupabaseClient();
    }

    public function show(string $id): void
    {
        // Clear cache if requested via URL parameter
        if (isset($_GET['clear_cache']) && $_GET['clear_cache'] === '1') {
            Cache::flush();
        }

        $projectModel = new Project();
        $topic = $projectModel->find($id);

        if (!$topic) {
            http_response_code(404);
            include __DIR__ . '/../Views/errors/404.php';
            return;
        }

        // Get all projects for the sidebar dropdown
        $allProjectsResult = $projectModel->all();
        $allProjects = $allProjectsResult['data'] ?? [];

        // Get tab from query string (default to overview)
        $tab = $_GET['tab'] ?? 'overview';

        // Get stats for the topic
        $stats = $this->getTopicStats($id);

        // Get data based on active tab
        $tabData = [];
        switch ($tab) {
            case 'prompts':
                $tabData = $this->getTopicPrompts($id);
                break;
            case 'sources':
                $tabData = $this->getTopicSources($id);
                break;
            case 'responses':
                $tabData = $this->getTopicResponses($id);
                break;
            case 'overview':
            default:
                $tab = 'overview';
                $tabData = $this->getTopicOverview($id);
                break;
        }

        $this->render('topics/show', [
            'pageTitle' => $topic['name'],
            'currentPage' => 'projects',
            'topic' => $topic,
            'allProjects' => $allProjects,
            'stats' => $stats,
            'activeTab' => $tab,
            'tabData' => $tabData,
        ], 'project');
    }

    // API endpoint for tab data
    public function apiTabData(string $id): void
    {
        header('Content-Type: application/json');

        $tab = $_GET['tab'] ?? 'responses';
        $offset = (int)($_GET['offset'] ?? 0);
        $limit = (int)($_GET['limit'] ?? 10);

        $data = [];
        switch ($tab) {
            case 'prompts':
                $data = $this->getTopicPrompts($id, $offset, $limit);
                break;
            case 'sources':
                $data = $this->getTopicSources($id, $offset, $limit);
                break;
            case 'overview':
                $data = $this->getTopicOverviewPaginated($id, $offset, $limit);
                break;
            case 'responses':
            default:
                $data = $this->getTopicResponses($id, $offset, $limit);
                break;
        }

        echo json_encode(['success' => true, 'data' => $data]);
    }

    private function getTopicStats(string $topicId): array
    {
        return Cache::remember("topic_stats_{$topicId}", function() use ($topicId) {
            // Get responses with prompts to count unique prompts
            $responsesResult = $this->db->from('ai_responses')
                ->select('id, prompt, model_name')
                ->eq('project_id', $topicId)
                ->get();
            $responsesCount = count($responsesResult['data'] ?? []);

            // Count unique prompts and models
            $uniquePrompts = [];
            $models = [];
            foreach ($responsesResult['data'] ?? [] as $r) {
                $promptText = trim($r['prompt'] ?? '');
                if (!empty($promptText)) {
                    $uniquePrompts[$promptText] = true;
                }
                $models[$r['model_name'] ?? ''] = true;
            }
            $promptsCount = count($uniquePrompts);

            // Get sources count from project_sources
            $sourcesResult = $this->db->from('project_sources')
                ->select('id')
                ->eq('project_id', $topicId)
                ->get();
            $sourcesCount = count($sourcesResult['data'] ?? []);

            // Get G-EVAL evaluations for this topic to calculate averages
            $evalResult = $this->db->from('evaluations')
                ->select('coherence, consistency, fluency, relevance, avg_score, ai_responses!inner(project_id)')
                ->eq('ai_responses.project_id', $topicId)
                ->get();

            $avgScore = 0;
            $evalCount = count($evalResult['data'] ?? []);

            if ($evalCount > 0) {
                $sumScore = 0;
                foreach ($evalResult['data'] as $e) {
                    // avg_score is already 0-100 percentage
                    $sumScore += (float)($e['avg_score'] ?? 0);
                }
                $avgScore = round($sumScore / $evalCount, 1);
            }

            return [
                'responsesCount' => $responsesCount,
                'promptsCount' => $promptsCount,
                'sourcesCount' => $sourcesCount,
                'modelsCount' => count($models),
                'avgAccuracy' => $avgScore, // Using avgScore for display
                'avgCompleteness' => 0,
                'avgNeutrality' => 0,
                'avgScore' => $avgScore,
            ];
        }, 300); // Cache for 5 minutes
    }

    private function getTopicResponses(string $topicId, int $offset = 0, int $limit = 10): array
    {
        // Get responses with G-EVAL evaluations from evaluations table
        $result = $this->db->from('evaluations')
            ->select('*, ai_responses!inner(id, prompt, response, model_name, project_id, created_at)')
            ->eq('ai_responses.project_id', $topicId)
            ->order('evaluated_at', false)
            ->offset($offset)
            ->limit($limit)
            ->get();

        $responses = [];
        foreach ($result['data'] ?? [] as $r) {
            $aiResponse = $r['ai_responses'] ?? [];
            // G-EVAL individual scores are 1-5 scale, multiply by 20 to get percentage
            // avg_score is already 0-100 percentage
            $coherence = (int)(($r['coherence'] ?? 0) * 20);
            $consistency = (int)(($r['consistency'] ?? 0) * 20);
            $fluency = (int)(($r['fluency'] ?? 0) * 20);
            $relevance = (int)(($r['relevance'] ?? 0) * 20);
            $avgScore = (int)($r['avg_score'] ?? 0);

            $responses[] = [
                'id' => $r['ai_response_id'],
                'prompt' => $this->truncateText($aiResponse['prompt'] ?? '', 150),
                'response' => $this->truncateText($aiResponse['response'] ?? '', 200),
                'model' => $aiResponse['model_name'] ?? '',
                'coherence' => $coherence,
                'consistency' => $consistency,
                'fluency' => $fluency,
                'relevance' => $relevance,
                'avgScore' => $avgScore,
                'tone' => $r['tone'] ?? 'neutral',
                'date' => $this->formatDate($r['evaluated_at'] ?? ''),
            ];
        }

        // If no evaluations, fall back to ai_responses
        if (empty($responses) && $offset === 0) {
            $fallbackResult = $this->db->from('ai_responses')
                ->select('*')
                ->eq('project_id', $topicId)
                ->order('created_at', false)
                ->offset($offset)
                ->limit($limit)
                ->get();

            foreach ($fallbackResult['data'] ?? [] as $r) {
                $responses[] = [
                    'id' => $r['id'],
                    'prompt' => $this->truncateText($r['prompt'] ?? '', 150),
                    'response' => $this->truncateText($r['response'] ?? '', 200),
                    'model' => $r['model_name'] ?? '',
                    'coherence' => 0,
                    'consistency' => 0,
                    'fluency' => 0,
                    'relevance' => 0,
                    'avgScore' => 0,
                    'tone' => $r['tone'] ?? 'neutral',
                    'date' => $this->formatDate($r['created_at'] ?? ''),
                ];
            }
        }

        // Cache total count
        $totalCount = Cache::remember("topic_responses_count_{$topicId}", function() use ($topicId) {
            $countResult = $this->db->from('evaluations')
                ->select('id, ai_responses!inner(project_id)')
                ->eq('ai_responses.project_id', $topicId)
                ->get();
            return count($countResult['data'] ?? []);
        }, 300);

        // Calculate model comparison (only on first page, cached)
        $modelStats = [];
        if ($offset === 0) {
            $modelStats = Cache::remember("topic_model_comparison_{$topicId}", function() use ($result, $topicId) {
                return $this->calculateModelComparison($result['data'] ?? [], $topicId);
            }, 300);
        }

        return [
            'responses' => $responses,
            'modelComparison' => $modelStats,
            'totalCount' => $totalCount,
            'hasMore' => ($offset + $limit) < $totalCount,
        ];
    }

    private function getTopicPrompts(string $topicId, int $offset = 0, int $limit = 10): array
    {
        // Cache all prompts data, then apply pagination
        // Prompts don't have G-EVAL scores - only responses do
        $cachedData = Cache::remember("topic_prompts_all_{$topicId}", function() use ($topicId) {
            // Get all responses to group by unique prompts
            $result = $this->db->from('ai_responses')
                ->select('prompt, model_name, created_at')
                ->eq('project_id', $topicId)
                ->order('created_at', false)
                ->get();

            // Group by unique prompt text
            $promptGroups = [];
            foreach ($result['data'] ?? [] as $r) {
                $promptText = trim($r['prompt'] ?? '');
                $modelName = $r['model_name'] ?? '';
                $date = $r['created_at'] ?? '';

                if (empty($promptText)) continue;

                if (!isset($promptGroups[$promptText])) {
                    $promptGroups[$promptText] = [
                        'text' => $promptText,
                        'count' => 0,
                        'latestDate' => $date,
                        'models' => [],
                    ];
                }

                $promptGroups[$promptText]['count']++;
                $promptGroups[$promptText]['models'][$modelName] = true;
            }

            // Convert to array
            $allPrompts = [];
            foreach ($promptGroups as $text => $group) {
                $allPrompts[] = [
                    'id' => md5($text),
                    'text' => $text,
                    'shortText' => mb_strlen($text) > 100 ? mb_substr($text, 0, 100) . '...' : $text,
                    'modelsCount' => count($group['models']),
                    'responsesCount' => $group['count'],
                    'date' => !empty($group['latestDate']) ? date('d.m.Y', strtotime($group['latestDate'])) : '',
                ];
            }

            return [
                'allPrompts' => $allPrompts,
                'totalCount' => count($allPrompts),
            ];
        }, 300); // Cache for 5 minutes

        // Apply pagination from cached data
        $prompts = array_slice($cachedData['allPrompts'], $offset, $limit);

        return [
            'prompts' => $prompts,
            'totalCount' => $cachedData['totalCount'],
            'hasMore' => ($offset + $limit) < $cachedData['totalCount'],
        ];
    }

    private function getTopicSources(string $topicId, int $offset = 0, int $limit = 10): array
    {
        // Try to get sources linked to this project via project_sources table
        $result = $this->db->from('project_sources')
            ->select('source_id, usage_count, sources(id, domain, type, country, expertise_score, experience_score, authority_score, trust_score, eeat_combined)')
            ->eq('project_id', $topicId)
            ->order('usage_count', false)
            ->offset($offset)
            ->limit($limit)
            ->get();

        $sources = [];
        $countryStats = [];
        $typeStats = [];
        $eeatSum = 0;

        // Process project_sources data if available
        foreach ($result['data'] ?? [] as $ps) {
            // Skip if not an array (malformed data)
            if (!is_array($ps)) continue;

            $s = $ps['sources'] ?? [];
            if (!is_array($s) || empty($s) || empty($s['id'])) continue;

            $eeat = (int)($s['eeat_combined'] ?? 0);
            $sources[] = [
                'id' => $s['id'],
                'domain' => $s['domain'] ?? '',
                'type' => $s['type'] ?? 'media',
                'country' => $s['country'] ?? 'OTHER',
                'domainRank' => 0,
                'urlRank' => 0,
                'experience' => (int)($s['experience_score'] ?? 0),
                'expertise' => (int)($s['expertise_score'] ?? 0),
                'authority' => (int)($s['authority_score'] ?? 0),
                'trust' => (int)($s['trust_score'] ?? 0),
                'eeat' => $eeat,
                'usage_count' => (int)($ps['usage_count'] ?? 1),
            ];

            $eeatSum += $eeat;

            // Country stats
            $country = $s['country'] ?? 'OTHER';
            $countryStats[$country] = ($countryStats[$country] ?? 0) + 1;

            // Type stats
            $type = $s['type'] ?? 'media';
            $typeStats[$type] = ($typeStats[$type] ?? 0) + 1;
        }

        // Track if we got data from project_sources
        $hasProjectSources = !empty($sources);

        // Fallback: If project_sources is empty, get sources from global sources table
        if (empty($sources)) {
            $fallbackResult = $this->db->from('sources')
                ->select('id, domain, type, country, expertise_score, experience_score, authority_score, trust_score, eeat_combined')
                ->order('eeat_combined', false)
                ->offset($offset)
                ->limit($limit)
                ->get();

            foreach ($fallbackResult['data'] ?? [] as $s) {
                // Skip if not an array (malformed data)
                if (!is_array($s)) continue;

                $eeat = (int)($s['eeat_combined'] ?? 0);
                $sources[] = [
                    'id' => $s['id'] ?? '',
                    'domain' => $s['domain'] ?? '',
                    'type' => $s['type'] ?? 'media',
                    'country' => $s['country'] ?? 'OTHER',
                    'domainRank' => 0,
                    'urlRank' => 0,
                    'experience' => (int)($s['experience_score'] ?? 0),
                    'expertise' => (int)($s['expertise_score'] ?? 0),
                    'authority' => (int)($s['authority_score'] ?? 0),
                    'trust' => (int)($s['trust_score'] ?? 0),
                    'eeat' => $eeat,
                    'usage_count' => 1,
                ];

                $eeatSum += $eeat;

                $country = $s['country'] ?? 'OTHER';
                $countryStats[$country] = ($countryStats[$country] ?? 0) + 1;

                $type = $s['type'] ?? 'media';
                $typeStats[$type] = ($typeStats[$type] ?? 0) + 1;
            }
        }

        // Get total count
        $totalCount = 0;
        if ($hasProjectSources) {
            $countResult = $this->db->from('project_sources')
                ->select('source_id')
                ->eq('project_id', $topicId)
                ->get();
            $totalCount = count($countResult['data'] ?? []);
        } else {
            // Fallback: count all sources
            $countResult = $this->db->from('sources')
                ->select('id')
                ->get();
            $totalCount = count($countResult['data'] ?? []);
        }

        $avgEeat = count($sources) > 0 ? round($eeatSum / count($sources), 1) : 0;

        // Find weak sources (EEAT < 50)
        $weakSources = array_filter($sources, fn($s) => $s['eeat'] < 50);

        return [
            'sources' => $sources,
            'totalCount' => $totalCount,
            'hasMore' => ($offset + $limit) < $totalCount,
            'avgEeat' => $avgEeat,
            'countryStats' => $countryStats,
            'typeStats' => $typeStats,
            'weakSources' => array_values($weakSources),
            'weakCount' => count($weakSources),
        ];
    }

    private function calculateModelComparison(array $evaluations, string $topicId = ''): array
    {
        if (empty($topicId)) {
            return [];
        }

        // Get all models and their response counts from ai_responses
        $allModels = [];
        $allResponsesResult = $this->db->from('ai_responses')
            ->select('model_name')
            ->eq('project_id', $topicId)
            ->get();

        foreach ($allResponsesResult['data'] ?? [] as $r) {
            $model = $r['model_name'] ?? 'Unknown';
            if (!isset($allModels[$model])) {
                $allModels[$model] = [
                    'name' => $model,
                    'count' => 0,
                    'totalScore' => 0,
                    'evalCount' => 0,
                ];
            }
            $allModels[$model]['count']++;
        }

        // Get ALL evaluations for this project (not just paginated)
        $allEvalsResult = $this->db->from('evaluations')
            ->select('avg_score, ai_responses!inner(model_name, project_id)')
            ->eq('ai_responses.project_id', $topicId)
            ->get();

        // Add evaluation scores from ALL evaluations
        foreach ($allEvalsResult['data'] ?? [] as $e) {
            $aiResponse = $e['ai_responses'] ?? [];
            $model = $aiResponse['model_name'] ?? 'Unknown';
            if (isset($allModels[$model])) {
                $allModels[$model]['totalScore'] += (float)($e['avg_score'] ?? 0);
                $allModels[$model]['evalCount']++;
            }
        }

        $result = [];
        foreach ($allModels as $name => $data) {
            $result[] = [
                'name' => $name,
                'count' => $data['count'],
                'avgScore' => $data['evalCount'] > 0 ? round($data['totalScore'] / $data['evalCount'], 1) : 0,
            ];
        }

        // Sort by avgScore desc
        usort($result, fn($a, $b) => $b['avgScore'] <=> $a['avgScore']);

        return $result;
    }

    private function truncateText(string $text, int $length): string
    {
        if (mb_strlen($text) <= $length) {
            return $text;
        }
        return mb_substr($text, 0, $length) . '...';
    }

    private function formatDate(string $date): string
    {
        if (empty($date)) {
            return '';
        }
        return date('d.m.Y', strtotime($date));
    }

    private function getTopicOverview(string $topicId): array
    {
        return Cache::remember("topic_overview_{$topicId}", function() use ($topicId) {
            // Get ALL evaluations with ai_responses for this project (for aggregations)
            $allEvalsResult = $this->db->from('evaluations')
                ->select('*, ai_responses!inner(id, prompt, model_name, project_id, created_at)')
                ->eq('ai_responses.project_id', $topicId)
                ->get();

            $allEvals = $allEvalsResult['data'] ?? [];
            $totalCount = count($allEvals);

            // Calculate G-Eval averages from ALL evaluations
            $totalCoherence = 0;
            $totalConsistency = 0;
            $totalFluency = 0;
            $totalRelevance = 0;
            $correctCount = 0;
            $problematicCount = 0;

            foreach ($allEvals as $r) {
                // G-EVAL individual scores are 1-5 scale, multiply by 20 to get percentage
                $coherence = (float)($r['coherence'] ?? 0) * 20;
                $consistency = (float)($r['consistency'] ?? 0) * 20;
                $fluency = (float)($r['fluency'] ?? 0) * 20;
                $relevance = (float)($r['relevance'] ?? 0) * 20;
                $avgScore = (float)($r['avg_score'] ?? 0);

                $totalCoherence += $coherence;
                $totalConsistency += $consistency;
                $totalFluency += $fluency;
                $totalRelevance += $relevance;

                // Count correct (>= 70) vs problematic (< 70) responses
                if ($avgScore >= 70) {
                    $correctCount++;
                } else {
                    $problematicCount++;
                }
            }

            // Get first 10 for display
            $displayEvals = array_slice($allEvals, 0, 10);
            $prompts = [];
            foreach ($displayEvals as $r) {
                $aiResponse = $r['ai_responses'] ?? [];
                $prompts[] = [
                    'id' => $r['ai_response_id'] ?? $r['id'],
                    'text' => $aiResponse['prompt'] ?? '',
                    'shortText' => $this->truncateText($aiResponse['prompt'] ?? '', 100),
                    'model' => $aiResponse['model_name'] ?? '',
                    'date' => $this->formatDate($r['evaluated_at'] ?? ''),
                    'coherence' => (int)(($r['coherence'] ?? 0) * 20),
                    'consistency' => (int)(($r['consistency'] ?? 0) * 20),
                    'fluency' => (int)(($r['fluency'] ?? 0) * 20),
                    'relevance' => (int)(($r['relevance'] ?? 0) * 20),
                    'avgScore' => (int)($r['avg_score'] ?? 0),
                ];
            }

            // Fallback to ai_responses if no evaluations
            if (empty($allEvals)) {
                $fallbackResult = $this->db->from('ai_responses')
                    ->select('*')
                    ->eq('project_id', $topicId)
                    ->order('created_at', false)
                    ->get();

                $totalCount = count($fallbackResult['data'] ?? []);
                $correctCount = $totalCount;
                $problematicCount = 0;

                foreach (array_slice($fallbackResult['data'] ?? [], 0, 10) as $r) {
                    $prompts[] = [
                        'id' => $r['id'],
                        'text' => $r['prompt'] ?? '',
                        'shortText' => $this->truncateText($r['prompt'] ?? '', 100),
                        'model' => $r['model_name'] ?? '',
                        'date' => $this->formatDate($r['created_at'] ?? ''),
                        'coherence' => 0,
                        'consistency' => 0,
                        'fluency' => 0,
                        'relevance' => 0,
                        'avgScore' => 0,
                    ];
                }
            }

            // Get sources data for this project
            $sourcesResult = $this->db->from('project_sources')
                ->select('source_id, usage_count, sources(id, domain, type, country, expertise_score, experience_score, authority_score, trust_score, eeat_combined)')
                ->eq('project_id', $topicId)
                ->get();

            $sourcesData = $sourcesResult['data'] ?? [];
            $totalSources = count($sourcesData);
            $frequentSources = 0;
            $notFoundSources = 0;
            $totalExperience = 0;
            $totalExpertise = 0;
            $totalAuthority = 0;
            $totalTrust = 0;
            $geoStats = [];
            $typeStats = [];

            foreach ($sourcesData as $ps) {
                $s = $ps['sources'] ?? [];
                if (!is_array($s) || empty($s)) continue;

                // Count frequent (usage > 1) vs not found
                $usageCount = (int)($ps['usage_count'] ?? 1);
                if ($usageCount > 1) {
                    $frequentSources++;
                }

                // E-E-A-T scores
                $totalExperience += (float)($s['experience_score'] ?? 0);
                $totalExpertise += (float)($s['expertise_score'] ?? 0);
                $totalAuthority += (float)($s['authority_score'] ?? 0);
                $totalTrust += (float)($s['trust_score'] ?? 0);

                // Geography distribution
                $country = $s['country'] ?? 'OTHER';
                $geoStats[$country] = ($geoStats[$country] ?? 0) + 1;

                // Type distribution
                $type = $s['type'] ?? 'media';
                $typeStats[$type] = ($typeStats[$type] ?? 0) + 1;
            }

            // If no project sources, get from global sources
            if ($totalSources === 0) {
                $globalSourcesResult = $this->db->from('sources')
                    ->select('id, domain, type, country, expertise_score, experience_score, authority_score, trust_score, eeat_combined')
                    ->limit(100)
                    ->get();

                foreach ($globalSourcesResult['data'] ?? [] as $s) {
                    $totalSources++;
                    $frequentSources++;

                    $totalExperience += (float)($s['experience_score'] ?? 0);
                    $totalExpertise += (float)($s['expertise_score'] ?? 0);
                    $totalAuthority += (float)($s['authority_score'] ?? 0);
                    $totalTrust += (float)($s['trust_score'] ?? 0);

                    $country = $s['country'] ?? 'OTHER';
                    $geoStats[$country] = ($geoStats[$country] ?? 0) + 1;

                    $type = $s['type'] ?? 'media';
                    $typeStats[$type] = ($typeStats[$type] ?? 0) + 1;
                }
            }

            // Get LLM distribution from ai_responses
            $llmResult = $this->db->from('ai_responses')
                ->select('model_name')
                ->eq('project_id', $topicId)
                ->get();

            $llmStats = [];
            foreach ($llmResult['data'] ?? [] as $r) {
                $model = $r['model_name'] ?? 'Unknown';
                $llmStats[$model] = ($llmStats[$model] ?? 0) + 1;
            }

            // Convert stats to chart format
            $geoDistribution = $this->convertToChartData($geoStats, [
                'KZ' => 'Казахстан',
                'RU' => 'Россия',
                'US' => 'США',
                'UK' => 'Великобритания',
                'OTHER' => 'Другие'
            ]);

            $typeDistribution = $this->convertToChartData($typeStats, [
                'media' => 'СМИ',
                'gov' => 'Гос. сайты',
                'analytics' => 'Аналитика',
                'blog' => 'Блоги',
                'social' => 'Соцсети',
                'wiki' => 'Wiki',
                'other' => 'Другие'
            ]);

            $llmDistribution = $this->convertToChartData($llmStats, []);

            $evalCount = count($allEvals) > 0 ? count($allEvals) : 1;
            $sourceCount = $totalSources > 0 ? $totalSources : 1;

            // Calculate G-Eval averages (for response quality)
            $avgCoherence = round($totalCoherence / $evalCount);
            $avgConsistency = round($totalConsistency / $evalCount);
            $avgFluency = round($totalFluency / $evalCount);
            $avgRelevance = round($totalRelevance / $evalCount);

            // Get prompt evaluations from prompt_evaluations table (for prompt quality radar)
            $promptEvalsResult = $this->db->from('prompt_evaluations')
                ->select('*')
                ->eq('project_id', $topicId)
                ->order('created_at', false)
                ->get();

            $promptEvals = $promptEvalsResult['data'] ?? [];
            $promptEvalCount = count($promptEvals);

            // Calculate prompt quality averages
            $avgNeutrality = 0;
            $avgStability = 0;
            $avgSoundness = 0;
            $avgAntiHallucination = 0;

            if ($promptEvalCount > 0) {
                $totalNeutrality = 0;
                $totalStability = 0;
                $totalSoundness = 0;
                $totalAntiHallucination = 0;

                foreach ($promptEvals as $pe) {
                    $totalNeutrality += (int)($pe['neutrality'] ?? 0);
                    $totalStability += (int)($pe['functional_stability'] ?? 0);
                    $totalSoundness += (int)($pe['logical_soundness'] ?? 0);
                    $totalAntiHallucination += (int)($pe['anti_hallucination'] ?? 0);
                }

                $avgNeutrality = round($totalNeutrality / $promptEvalCount);
                $avgStability = round($totalStability / $promptEvalCount);
                $avgSoundness = round($totalSoundness / $promptEvalCount);
                $avgAntiHallucination = round($totalAntiHallucination / $promptEvalCount);
            }

            return [
                'prompts' => $prompts,
                'totalCount' => $totalCount,
                'hasMore' => $totalCount > 10,

                // Prompt quality radar (4 params from prompt_evaluations table)
                'radarData' => [
                    'neutrality' => $avgNeutrality,
                    'stability' => $avgStability,
                    'soundness' => $avgSoundness,
                    'antiHallucination' => $avgAntiHallucination,
                ],

                // Prompt evaluations list for display
                'promptEvaluations' => array_slice($promptEvals, 0, 10),
                'promptEvaluationsCount' => $promptEvalCount,
                'hasMorePromptEvals' => $promptEvalCount > 10,

                // Response stats (3 hero cards)
                'responsesStats' => [
                    'total' => $totalCount,
                    'correct' => $correctCount,
                    'problematic' => $problematicCount,
                ],

                // G-Eval radar data (4 params)
                'gEvalData' => [
                    'coherence' => $avgCoherence,
                    'consistency' => $avgConsistency,
                    'fluency' => $avgFluency,
                    'relevance' => $avgRelevance,
                ],

                // Average scores for progress bars
                'promptQuality' => [
                    'avgScore' => $promptEvalCount > 0 ? round(($avgNeutrality + $avgStability + $avgSoundness + $avgAntiHallucination) / 4) : 0,
                ],

                // Sources stats (3 hero cards)
                'sourcesStats' => [
                    'total' => $totalSources,
                    'frequent' => $frequentSources,
                    'notFound' => $notFoundSources,
                ],

                // E-E-A-T radar data (4 params)
                'eeatData' => [
                    'experience' => round($totalExperience / $sourceCount),
                    'expertise' => round($totalExpertise / $sourceCount),
                    'authoritativeness' => round($totalAuthority / $sourceCount),
                    'trustworthiness' => round($totalTrust / $sourceCount),
                ],

                // Pie chart distributions
                'geoDistribution' => $geoDistribution,
                'typeDistribution' => $typeDistribution,
                'llmDistribution' => $llmDistribution,
            ];
        }, 300); // Cache for 5 minutes
    }

    private function convertToChartData(array $stats, array $labelMap): array
    {
        if (empty($stats)) {
            return [];
        }

        $total = array_sum($stats);
        if ($total === 0) {
            return [];
        }

        // Sort by value desc
        arsort($stats);

        $result = [];
        foreach ($stats as $key => $value) {
            $label = !empty($labelMap) ? ($labelMap[$key] ?? $key) : $key;
            $result[] = [
                'label' => $label,
                'value' => round(($value / $total) * 100),
            ];
        }

        return $result;
    }

    private function getTopicOverviewPaginated(string $topicId, int $offset = 0, int $limit = 10): array
    {
        // Get evaluations with ai_responses for pagination
        $evalsResult = $this->db->from('evaluations')
            ->select('*, ai_responses!inner(id, prompt, model_name, project_id)')
            ->eq('ai_responses.project_id', $topicId)
            ->order('evaluated_at', false)
            ->offset($offset)
            ->limit($limit)
            ->get();

        // Get total count (cached)
        $totalCount = Cache::remember("topic_overview_count_{$topicId}", function() use ($topicId) {
            $countResult = $this->db->from('evaluations')
                ->select('id, ai_responses!inner(project_id)')
                ->eq('ai_responses.project_id', $topicId)
                ->get();
            return count($countResult['data'] ?? []);
        }, 300);

        $prompts = [];
        foreach ($evalsResult['data'] ?? [] as $r) {
            $aiResponse = $r['ai_responses'] ?? [];
            // G-EVAL individual scores are 1-5 scale, multiply by 20 to get percentage
            $prompts[] = [
                'id' => $r['ai_response_id'] ?? $r['id'],
                'text' => $aiResponse['prompt'] ?? '',
                'shortText' => $this->truncateText($aiResponse['prompt'] ?? '', 100),
                'model' => $aiResponse['model_name'] ?? '',
                'date' => $this->formatDate($r['evaluated_at'] ?? ''),
                'coherence' => (int)(($r['coherence'] ?? 0) * 20),
                'consistency' => (int)(($r['consistency'] ?? 0) * 20),
                'fluency' => (int)(($r['fluency'] ?? 0) * 20),
                'relevance' => (int)(($r['relevance'] ?? 0) * 20),
                'avgScore' => (int)($r['avg_score'] ?? 0),
            ];
        }

        return [
            'prompts' => $prompts,
            'totalCount' => $totalCount,
            'hasMore' => ($offset + $limit) < $totalCount,
        ];
    }
}
