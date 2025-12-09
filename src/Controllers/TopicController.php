<?php

namespace App\Controllers;

use App\Models\Project;
use App\Models\AiResponse;
use App\Models\Source;
use App\Models\Evaluation;
use App\Services\SupabaseClient;

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
        // Get responses count
        $responsesResult = $this->db->from('ai_responses')
            ->select('id')
            ->eq('project_id', $topicId)
            ->get();
        $responsesCount = count($responsesResult['data'] ?? []);

        // Get unique models used
        $modelsResult = $this->db->from('ai_responses')
            ->select('model_name')
            ->eq('project_id', $topicId)
            ->get();
        $models = [];
        foreach ($modelsResult['data'] ?? [] as $r) {
            $models[$r['model_name']] = true;
        }

        // Get sources count from project_sources
        $sourcesResult = $this->db->from('project_sources')
            ->select('id')
            ->eq('project_id', $topicId)
            ->get();
        $sourcesCount = count($sourcesResult['data'] ?? []);

        // Get evaluations for this topic to calculate averages
        $evalResult = $this->db->from('recent_evaluations_detailed')
            ->select('*')
            ->eq('project_id', $topicId)
            ->get();

        $avgAccuracy = 0;
        $avgCompleteness = 0;
        $avgNeutrality = 0;
        $evalCount = count($evalResult['data'] ?? []);

        if ($evalCount > 0) {
            $sumAcc = 0;
            $sumComp = 0;
            $sumNeut = 0;
            foreach ($evalResult['data'] as $e) {
                $sumAcc += (float)($e['accuracy_score'] ?? $e['avg_score'] ?? 0);
                $sumComp += (float)($e['completeness_score'] ?? $e['avg_score'] ?? 0);
                $sumNeut += (float)($e['neutrality_score'] ?? $e['avg_score'] ?? 0);
            }
            $avgAccuracy = round($sumAcc / $evalCount, 1);
            $avgCompleteness = round($sumComp / $evalCount, 1);
            $avgNeutrality = round($sumNeut / $evalCount, 1);
        }

        return [
            'responsesCount' => $responsesCount,
            'promptsCount' => $responsesCount, // prompts = responses in our structure
            'sourcesCount' => $sourcesCount,
            'modelsCount' => count($models),
            'avgAccuracy' => $avgAccuracy,
            'avgCompleteness' => $avgCompleteness,
            'avgNeutrality' => $avgNeutrality,
            'avgScore' => round(($avgAccuracy + $avgCompleteness + $avgNeutrality) / 3, 1),
        ];
    }

    private function getTopicResponses(string $topicId, int $offset = 0, int $limit = 10): array
    {
        // Get responses with evaluations
        $result = $this->db->from('recent_evaluations_detailed')
            ->select('*')
            ->eq('project_id', $topicId)
            ->order('evaluated_at', false)
            ->offset($offset)
            ->limit($limit)
            ->get();

        $responses = [];
        foreach ($result['data'] ?? [] as $r) {
            $responses[] = [
                'id' => $r['ai_response_id'],
                'prompt' => $this->truncateText($r['prompt'] ?? '', 150),
                'response' => $this->truncateText($r['response'] ?? '', 200),
                'model' => $r['model_name'] ?? '',
                'accuracy' => (int)($r['accuracy_score'] ?? $r['avg_score'] ?? 0),
                'completeness' => (int)($r['completeness_score'] ?? $r['avg_score'] ?? 0),
                'neutrality' => (int)($r['neutrality_score'] ?? $r['avg_score'] ?? 0),
                'relevance' => (int)($r['relevance_score'] ?? $r['avg_score'] ?? 0),
                'clarity' => (int)($r['clarity_score'] ?? $r['avg_score'] ?? 0),
                'avgScore' => (int)($r['avg_score'] ?? 0),
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
                    'accuracy' => 0,
                    'completeness' => 0,
                    'neutrality' => 0,
                    'relevance' => 0,
                    'clarity' => 0,
                    'avgScore' => 0,
                    'tone' => $r['tone'] ?? 'neutral',
                    'date' => $this->formatDate($r['created_at'] ?? ''),
                ];
            }
        }

        // Get total count for pagination
        $countResult = $this->db->from('recent_evaluations_detailed')
            ->select('id')
            ->eq('project_id', $topicId)
            ->get();
        $totalCount = count($countResult['data'] ?? []);

        // Calculate model comparison (only on first page)
        $modelStats = $offset === 0 ? $this->calculateModelComparison($result['data'] ?? []) : [];

        return [
            'responses' => $responses,
            'modelComparison' => $modelStats,
            'totalCount' => $totalCount,
            'hasMore' => ($offset + $limit) < $totalCount,
        ];
    }

    private function getTopicPrompts(string $topicId, int $offset = 0, int $limit = 10): array
    {
        // Get prompts with evaluations from recent_evaluations_detailed
        $result = $this->db->from('recent_evaluations_detailed')
            ->select('*')
            ->eq('project_id', $topicId)
            ->order('evaluated_at', false)
            ->offset($offset)
            ->limit($limit)
            ->get();

        $prompts = [];
        $promptGroups = [];

        foreach ($result['data'] ?? [] as $r) {
            $prompt = [
                'id' => $r['ai_response_id'] ?? $r['id'],
                'text' => $r['prompt'] ?? '',
                'shortText' => $this->truncateText($r['prompt'] ?? '', 100),
                'model' => $r['model_name'] ?? '',
                'date' => $this->formatDate($r['evaluated_at'] ?? $r['created_at'] ?? ''),
                'specificity' => (int)($r['accuracy_score'] ?? $r['avg_score'] ?? 0),
                'completeness' => (int)($r['completeness_score'] ?? $r['avg_score'] ?? 0),
                'neutrality' => (int)($r['neutrality_score'] ?? $r['avg_score'] ?? 0),
                'topicality' => (int)($r['relevance_score'] ?? $r['avg_score'] ?? 0),
            ];
            $prompts[] = $prompt;

            // Simple grouping by first words
            $firstWords = implode(' ', array_slice(explode(' ', $r['prompt'] ?? ''), 0, 3));
            if (!isset($promptGroups[$firstWords])) {
                $promptGroups[$firstWords] = [];
            }
            $promptGroups[$firstWords][] = $prompt;
        }

        // Fallback to ai_responses if no evaluations
        if (empty($prompts) && $offset === 0) {
            $fallbackResult = $this->db->from('ai_responses')
                ->select('*')
                ->eq('project_id', $topicId)
                ->order('created_at', false)
                ->offset($offset)
                ->limit($limit)
                ->get();

            foreach ($fallbackResult['data'] ?? [] as $r) {
                $prompt = [
                    'id' => $r['id'],
                    'text' => $r['prompt'] ?? '',
                    'shortText' => $this->truncateText($r['prompt'] ?? '', 100),
                    'model' => $r['model_name'] ?? '',
                    'date' => $this->formatDate($r['created_at'] ?? ''),
                    'specificity' => 0,
                    'completeness' => 0,
                    'neutrality' => 0,
                    'topicality' => 0,
                ];
                $prompts[] = $prompt;
            }
        }

        // Get total count
        $countResult = $this->db->from('ai_responses')
            ->select('id')
            ->eq('project_id', $topicId)
            ->get();
        $totalCount = count($countResult['data'] ?? []);

        // Calculate prompt quality stats
        $avgSpecificity = 0;
        $avgCompleteness = 0;
        $avgNeutrality = 0;
        $count = count($prompts);

        if ($count > 0) {
            foreach ($prompts as $p) {
                $avgSpecificity += $p['specificity'];
                $avgCompleteness += $p['completeness'];
                $avgNeutrality += $p['neutrality'];
            }
            $avgSpecificity = round($avgSpecificity / $count, 1);
            $avgCompleteness = round($avgCompleteness / $count, 1);
            $avgNeutrality = round($avgNeutrality / $count, 1);
        }

        return [
            'prompts' => $prompts,
            'groups' => array_slice($promptGroups, 0, 10), // Top 10 groups
            'totalCount' => $totalCount,
            'hasMore' => ($offset + $limit) < $totalCount,
            'stats' => [
                'avgSpecificity' => $avgSpecificity,
                'avgCompleteness' => $avgCompleteness,
                'avgNeutrality' => $avgNeutrality,
            ],
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
            ->select('id')
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
            $model = $e['model_name'] ?? 'Unknown';
            if (!isset($models[$model])) {
                $models[$model] = [
                    'name' => $model,
                    'count' => 0,
                    'totalScore' => 0,
                    'totalAccuracy' => 0,
                ];
            }
            $models[$model]['count']++;
            $models[$model]['totalScore'] += (float)($e['avg_score'] ?? 0);
            $models[$model]['totalAccuracy'] += (float)($e['accuracy_score'] ?? $e['avg_score'] ?? 0);
        }

        $result = [];
        foreach ($models as $name => $data) {
            $result[] = [
                'name' => $name,
                'count' => $data['count'],
                'avgScore' => $data['count'] > 0 ? round($data['totalScore'] / $data['count'], 1) : 0,
                'avgAccuracy' => $data['count'] > 0 ? round($data['totalAccuracy'] / $data['count'], 1) : 0,
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
        // Get total count first
        $countResult = $this->db->from('recent_evaluations_detailed')
            ->select('id')
            ->eq('project_id', $topicId)
            ->get();
        $totalCount = count($countResult['data'] ?? []);

        // Get first 10 prompts with evaluations
        $promptsResult = $this->db->from('recent_evaluations_detailed')
            ->select('*')
            ->eq('project_id', $topicId)
            ->order('evaluated_at', false)
            ->limit(10)
            ->get();

        $prompts = [];
        $totalSpecificity = 0;
        $totalCompleteness = 0;
        $totalNeutrality = 0;
        $totalClarity = 0;
        $totalTaskType = 0;

        foreach ($promptsResult['data'] ?? [] as $r) {
            // Use real scores from evaluations
            $specificity = (int)($r['accuracy_score'] ?? $r['avg_score'] ?? 0);
            $completeness = (int)($r['completeness_score'] ?? $r['avg_score'] ?? 0);
            $neutrality = (int)($r['neutrality_score'] ?? $r['avg_score'] ?? 0);
            $clarity = (int)($r['clarity_score'] ?? $r['avg_score'] ?? 0);
            $taskType = (int)($r['relevance_score'] ?? $r['avg_score'] ?? 0);

            $prompts[] = [
                'id' => $r['ai_response_id'] ?? $r['id'],
                'text' => $r['prompt'] ?? '',
                'shortText' => $this->truncateText($r['prompt'] ?? '', 100),
                'model' => $r['model_name'] ?? '',
                'date' => $this->formatDate($r['evaluated_at'] ?? $r['created_at'] ?? ''),
                'specificity' => $specificity,
                'completeness' => $completeness,
                'neutrality' => $neutrality,
                'clarity' => $clarity,
                'taskType' => $taskType,
            ];

            $totalSpecificity += $specificity;
            $totalCompleteness += $completeness;
            $totalNeutrality += $neutrality;
            $totalClarity += $clarity;
            $totalTaskType += $taskType;
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
                    'specificity' => 0,
                    'completeness' => 0,
                    'neutrality' => 0,
                    'clarity' => 0,
                    'taskType' => 0,
                ];
            }
        }

        $count = count($prompts);

        // Calculate averages for radar chart
        $radarData = [
            'specificity' => $count > 0 ? round($totalSpecificity / $count) : 0,
            'completeness' => $count > 0 ? round($totalCompleteness / $count) : 0,
            'neutrality' => $count > 0 ? round($totalNeutrality / $count) : 0,
            'clarity' => $count > 0 ? round($totalClarity / $count) : 0,
            'taskType' => $count > 0 ? round($totalTaskType / $count) : 0,
        ];

        return [
            'prompts' => $prompts,
            'totalCount' => $totalCount,
            'hasMore' => $totalCount > 10,
            'radarData' => $radarData,
        ];
    }

    private function getTopicOverviewPaginated(string $topicId, int $offset = 0, int $limit = 10): array
    {
        // Get prompts with pagination from evaluations
        $promptsResult = $this->db->from('recent_evaluations_detailed')
            ->select('*')
            ->eq('project_id', $topicId)
            ->order('evaluated_at', false)
            ->offset($offset)
            ->limit($limit)
            ->get();

        // Get total count
        $countResult = $this->db->from('recent_evaluations_detailed')
            ->select('id')
            ->eq('project_id', $topicId)
            ->get();
        $totalCount = count($countResult['data'] ?? []);

        $prompts = [];
        foreach ($promptsResult['data'] ?? [] as $r) {
            $prompts[] = [
                'id' => $r['ai_response_id'] ?? $r['id'],
                'text' => $r['prompt'] ?? '',
                'shortText' => $this->truncateText($r['prompt'] ?? '', 100),
                'model' => $r['model_name'] ?? '',
                'date' => $this->formatDate($r['evaluated_at'] ?? $r['created_at'] ?? ''),
                'specificity' => (int)($r['accuracy_score'] ?? $r['avg_score'] ?? 0),
                'completeness' => (int)($r['completeness_score'] ?? $r['avg_score'] ?? 0),
                'neutrality' => (int)($r['neutrality_score'] ?? $r['avg_score'] ?? 0),
                'clarity' => (int)($r['clarity_score'] ?? $r['avg_score'] ?? 0),
                'taskType' => (int)($r['relevance_score'] ?? $r['avg_score'] ?? 0),
            ];
        }

        return [
            'prompts' => $prompts,
            'totalCount' => $totalCount,
            'hasMore' => ($offset + $limit) < $totalCount,
        ];
    }
}
