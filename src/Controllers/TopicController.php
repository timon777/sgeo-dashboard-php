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
            // G-EVAL scores are already 0-100 percentages
            $coherence = (int)($r['coherence'] ?? 0);
            $consistency = (int)($r['consistency'] ?? 0);
            $fluency = (int)($r['fluency'] ?? 0);
            $relevance = (int)($r['relevance'] ?? 0);
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
            $modelStats = Cache::remember("topic_model_comparison_{$topicId}", function() use ($result) {
                return $this->calculateModelComparison($result['data'] ?? []);
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
        $cachedData = Cache::remember("topic_prompts_all_{$topicId}", function() use ($topicId) {
            // Try to get evaluations with G-EVAL metrics first
            $result = $this->db->from('evaluations')
                ->select('coherence, consistency, fluency, relevance, avg_score, evaluated_at, ai_responses!inner(prompt, model_name, project_id, created_at)')
                ->eq('ai_responses.project_id', $topicId)
                ->order('evaluated_at', false)
                ->get();

            $hasEvaluations = !empty($result['data']);

            // If no evaluations, fall back to ai_responses
            if (!$hasEvaluations) {
                $result = $this->db->from('ai_responses')
                    ->select('prompt, model_name, created_at')
                    ->eq('project_id', $topicId)
                    ->order('created_at', false)
                    ->get();
            }

            // Group by unique prompt text
            $promptGroups = [];
            foreach ($result['data'] ?? [] as $r) {
                if ($hasEvaluations) {
                    $aiResponse = $r['ai_responses'] ?? [];
                    $promptText = trim($aiResponse['prompt'] ?? '');
                    $modelName = $aiResponse['model_name'] ?? '';
                    $date = $r['evaluated_at'] ?? $aiResponse['created_at'] ?? '';
                    $coherence = (float)($r['coherence'] ?? 0);
                    $consistency = (float)($r['consistency'] ?? 0);
                    $fluency = (float)($r['fluency'] ?? 0);
                    $relevance = (float)($r['relevance'] ?? 0);
                } else {
                    $promptText = trim($r['prompt'] ?? '');
                    $modelName = $r['model_name'] ?? '';
                    $date = $r['created_at'] ?? '';
                    $coherence = 0;
                    $consistency = 0;
                    $fluency = 0;
                    $relevance = 0;
                }

                if (empty($promptText)) continue;

                if (!isset($promptGroups[$promptText])) {
                    $promptGroups[$promptText] = [
                        'text' => $promptText,
                        'totalCoherence' => 0,
                        'totalConsistency' => 0,
                        'totalFluency' => 0,
                        'totalRelevance' => 0,
                        'count' => 0,
                        'latestDate' => $date,
                        'models' => [],
                    ];
                }

                $promptGroups[$promptText]['totalCoherence'] += $coherence;
                $promptGroups[$promptText]['totalConsistency'] += $consistency;
                $promptGroups[$promptText]['totalFluency'] += $fluency;
                $promptGroups[$promptText]['totalRelevance'] += $relevance;
                $promptGroups[$promptText]['count']++;
                $promptGroups[$promptText]['models'][$modelName] = true;
            }

            // Convert to array with average scores
            $allPrompts = [];
            $totalCoherence = 0;
            $totalConsistency = 0;
            $totalFluency = 0;

            foreach ($promptGroups as $text => $group) {
                $count = $group['count'];
                $coherence = $count > 0 ? (int)round($group['totalCoherence'] / $count) : 0;
                $consistency = $count > 0 ? (int)round($group['totalConsistency'] / $count) : 0;
                $fluency = $count > 0 ? (int)round($group['totalFluency'] / $count) : 0;
                $relevance = $count > 0 ? (int)round($group['totalRelevance'] / $count) : 0;

                $allPrompts[] = [
                    'id' => md5($text),
                    'text' => $text,
                    'shortText' => mb_strlen($text) > 100 ? mb_substr($text, 0, 100) . '...' : $text,
                    'modelsCount' => count($group['models']),
                    'responsesCount' => $count,
                    'date' => !empty($group['latestDate']) ? date('d.m.Y', strtotime($group['latestDate'])) : '',
                    'coherence' => $coherence,
                    'consistency' => $consistency,
                    'fluency' => $fluency,
                    'relevance' => $relevance,
                ];

                $totalCoherence += $coherence;
                $totalConsistency += $consistency;
                $totalFluency += $fluency;
            }

            $totalCount = count($allPrompts);

            return [
                'allPrompts' => $allPrompts,
                'totalCount' => $totalCount,
                'stats' => [
                    'avgCoherence' => $totalCount > 0 ? round($totalCoherence / $totalCount, 1) : 0,
                    'avgConsistency' => $totalCount > 0 ? round($totalConsistency / $totalCount, 1) : 0,
                    'avgFluency' => $totalCount > 0 ? round($totalFluency / $totalCount, 1) : 0,
                ],
            ];
        }, 300); // Cache for 5 minutes

        // Apply pagination from cached data
        $prompts = array_slice($cachedData['allPrompts'], $offset, $limit);

        return [
            'prompts' => $prompts,
            'totalCount' => $cachedData['totalCount'],
            'hasMore' => ($offset + $limit) < $cachedData['totalCount'],
            'stats' => $cachedData['stats'],
        ];
    }

    private function getTopicSources(string $topicId, int $offset = 0, int $limit = 10): array
    {
        // Get sources linked to this project via project_sources table
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

        foreach ($result['data'] ?? [] as $ps) {
            $s = $ps['sources'] ?? [];
            if (empty($s)) continue;

            $eeat = (int)($s['eeat_combined'] ?? 0);
            $sources[] = [
                'id' => $s['id'],
                'domain' => $s['domain'] ?? '',
                'type' => $s['type'] ?? 'media',
                'country' => $s['country'] ?? 'OTHER',
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

        // Get total count
        $countResult = $this->db->from('project_sources')
            ->select('source_id')
            ->eq('project_id', $topicId)
            ->get();
        $totalCount = count($countResult['data'] ?? []);

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

    private function calculateModelComparison(array $evaluations): array
    {
        $models = [];

        foreach ($evaluations as $e) {
            $aiResponse = $e['ai_responses'] ?? [];
            $model = $aiResponse['model_name'] ?? 'Unknown';
            if (!isset($models[$model])) {
                $models[$model] = [
                    'name' => $model,
                    'count' => 0,
                    'totalScore' => 0,
                ];
            }
            $models[$model]['count']++;
            // avg_score is already 0-100 percentage
            $models[$model]['totalScore'] += (float)($e['avg_score'] ?? 0);
        }

        $result = [];
        foreach ($models as $name => $data) {
            $result[] = [
                'name' => $name,
                'count' => $data['count'],
                'avgScore' => $data['count'] > 0 ? round($data['totalScore'] / $data['count'], 1) : 0,
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
            // Get evaluations with ai_responses for this project
            $evalsResult = $this->db->from('evaluations')
                ->select('*, ai_responses!inner(id, prompt, model_name, project_id, created_at)')
                ->eq('ai_responses.project_id', $topicId)
                ->order('evaluated_at', false)
                ->limit(10)
                ->get();

            // Get total count
            $countResult = $this->db->from('evaluations')
                ->select('id, ai_responses!inner(project_id)')
                ->eq('ai_responses.project_id', $topicId)
                ->get();
            $totalCount = count($countResult['data'] ?? []);

            $prompts = [];
            $totalCoherence = 0;
            $totalConsistency = 0;
            $totalFluency = 0;
            $totalRelevance = 0;

            foreach ($evalsResult['data'] ?? [] as $r) {
                $aiResponse = $r['ai_responses'] ?? [];
                // G-EVAL scores are already 0-100 percentages
                $coherence = (int)($r['coherence'] ?? 0);
                $consistency = (int)($r['consistency'] ?? 0);
                $fluency = (int)($r['fluency'] ?? 0);
                $relevance = (int)($r['relevance'] ?? 0);

                $prompts[] = [
                    'id' => $r['ai_response_id'] ?? $r['id'],
                    'text' => $aiResponse['prompt'] ?? '',
                    'shortText' => $this->truncateText($aiResponse['prompt'] ?? '', 100),
                    'model' => $aiResponse['model_name'] ?? '',
                    'date' => $this->formatDate($r['evaluated_at'] ?? ''),
                    'coherence' => $coherence,
                    'consistency' => $consistency,
                    'fluency' => $fluency,
                    'relevance' => $relevance,
                    'avgScore' => (int)($r['avg_score'] ?? 0),
                ];

                $totalCoherence += $coherence;
                $totalConsistency += $consistency;
                $totalFluency += $fluency;
                $totalRelevance += $relevance;
            }

            // Fallback to ai_responses if no evaluations
            if (empty($prompts)) {
                $fallbackResult = $this->db->from('ai_responses')
                    ->select('*')
                    ->eq('project_id', $topicId)
                    ->order('created_at', false)
                    ->limit(10)
                    ->get();

                $totalCount = count($fallbackResult['data'] ?? []);

                foreach ($fallbackResult['data'] ?? [] as $r) {
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

            $count = count($prompts);

            return [
                'prompts' => $prompts,
                'totalCount' => $totalCount,
                'hasMore' => $totalCount > 10,
                'radarData' => [
                    'coherence' => $count > 0 ? round($totalCoherence / $count) : 0,
                    'consistency' => $count > 0 ? round($totalConsistency / $count) : 0,
                    'fluency' => $count > 0 ? round($totalFluency / $count) : 0,
                    'relevance' => $count > 0 ? round($totalRelevance / $count) : 0,
                ],
            ];
        }, 300); // Cache for 5 minutes
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
            $prompts[] = [
                'id' => $r['ai_response_id'] ?? $r['id'],
                'text' => $aiResponse['prompt'] ?? '',
                'shortText' => $this->truncateText($aiResponse['prompt'] ?? '', 100),
                'model' => $aiResponse['model_name'] ?? '',
                'date' => $this->formatDate($r['evaluated_at'] ?? ''),
                'coherence' => (int)($r['coherence'] ?? 0),
                'consistency' => (int)($r['consistency'] ?? 0),
                'fluency' => (int)($r['fluency'] ?? 0),
                'relevance' => (int)($r['relevance'] ?? 0),
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
