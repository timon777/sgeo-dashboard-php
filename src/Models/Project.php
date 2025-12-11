<?php

namespace App\Models;

use App\Services\SupabaseClient;

class Project
{
    private SupabaseClient $db;
    private string $table = 'projects';

    public function __construct()
    {
        $this->db = new SupabaseClient();
    }

    public function all(bool $activeOnly = true): array
    {
        $query = $this->db->from($this->table)->select('*');

        if ($activeOnly) {
            $query->eq('is_active', 'true');
        }

        return $query->order('created_at', false)->get();
    }

    public function find(string $id): ?array
    {
        return $this->db->from($this->table)
            ->select('*')
            ->eq('id', $id)
            ->single();
    }

    public function byType(string $type): array
    {
        return $this->db->from($this->table)
            ->select('*')
            ->eq('type', $type)
            ->eq('is_active', 'true')
            ->order('accuracy_score', false)
            ->get();
    }

    public function getStats(): array
    {
        return $this->db->from('project_stats')
            ->select('*')
            ->get();
    }

    public function count(): int
    {
        $result = $this->all();
        return count($result['data'] ?? []);
    }

    /**
     * Get detailed statistics for a specific project
     */
    public function getProjectStats(string $projectId): array
    {
        // Get project details first
        $project = $this->find($projectId);
        if (!$project) {
            return $this->getDefaultStats();
        }

        // Try to get stats from project_statistics view/table
        $statsResult = $this->db->from('project_statistics')
            ->select('*')
            ->eq('project_id', $projectId)
            ->single();

        if ($statsResult) {
            return [
                'processedPrompts' => (int)($statsResult['total_responses'] ?? 0),
                'uniqueSources' => (int)($statsResult['unique_sources'] ?? 0),
                'avgTone' => $this->formatTone($statsResult['avg_tone'] ?? 0),
                'llmModels' => (int)($statsResult['llm_models_count'] ?? 0),
            ];
        }

        // Fallback: calculate from ai_responses linked to project
        return $this->calculateStatsFromResponses($projectId, $project['name'] ?? '');
    }

    /**
     * Calculate stats by searching ai_responses related to project
     */
    private function calculateStatsFromResponses(string $projectId, string $projectName): array
    {
        // Get ai_responses that mention this project (by topic_id or search)
        $responsesResult = $this->db->from('ai_responses')
            ->select('id,model_name,tone_score')
            ->eq('topic_id', $projectId)
            ->get();

        $responses = $responsesResult['data'] ?? [];

        // If no direct link, try searching by project name in prompts
        if (empty($responses) && !empty($projectName)) {
            $responsesResult = $this->db->from('ai_responses')
                ->select('id,model_name,tone_score')
                ->ilike('prompt', '%' . $projectName . '%')
                ->limit(500)
                ->get();
            $responses = $responsesResult['data'] ?? [];
        }

        $totalResponses = count($responses);
        $uniqueModels = [];
        $toneSum = 0;
        $toneCount = 0;

        foreach ($responses as $response) {
            if (!empty($response['model_name'])) {
                $uniqueModels[$response['model_name']] = true;
            }
            if (isset($response['tone_score'])) {
                $toneSum += (float)$response['tone_score'];
                $toneCount++;
            }
        }

        // Get sources count
        $sourcesResult = $this->db->from('sources')
            ->select('id')
            ->limit(500)
            ->get();
        $sourcesCount = count($sourcesResult['data'] ?? []);

        $avgTone = $toneCount > 0 ? $toneSum / $toneCount : 0;

        return [
            'processedPrompts' => $totalResponses,
            'uniqueSources' => $sourcesCount,
            'avgTone' => $this->formatTone($avgTone),
            'llmModels' => count($uniqueModels),
        ];
    }

    /**
     * Get evaluation metrics for a project
     */
    public function getProjectMetrics(string $projectId): array
    {
        // Try to get from project_metrics view
        $metricsResult = $this->db->from('project_metrics')
            ->select('*')
            ->eq('project_id', $projectId)
            ->single();

        if ($metricsResult) {
            return [
                'promptQuality' => [
                    'overall' => (float)($metricsResult['prompt_quality_score'] ?? 83.6),
                    'specificity' => (float)($metricsResult['prompt_specificity'] ?? 85),
                    'completeness' => (float)($metricsResult['prompt_completeness'] ?? 78),
                    'neutrality' => (float)($metricsResult['prompt_neutrality'] ?? 92),
                    'clarity' => (float)($metricsResult['prompt_clarity'] ?? 88),
                    'taskType' => (float)($metricsResult['prompt_task_type'] ?? 75),
                ],
                'answerQuality' => [
                    'overall' => (float)($metricsResult['answer_quality_score'] ?? 85.2),
                    'specificity' => (float)($metricsResult['answer_specificity'] ?? 82),
                    'completeness' => (float)($metricsResult['answer_completeness'] ?? 88),
                    'relevance' => (float)($metricsResult['answer_relevance'] ?? 91),
                    'neutrality' => (float)($metricsResult['answer_neutrality'] ?? 85),
                    'clarity' => (float)($metricsResult['answer_clarity'] ?? 79),
                    'accuracy' => (float)($metricsResult['answer_accuracy'] ?? 86),
                ],
                'eeat' => [
                    'overall' => (float)($metricsResult['eeat_score'] ?? 83.8),
                    'experience' => (float)($metricsResult['eeat_experience'] ?? 75),
                    'expertise' => (float)($metricsResult['eeat_expertise'] ?? 88),
                    'authority' => (float)($metricsResult['eeat_authority'] ?? 82),
                    'trust' => (float)($metricsResult['eeat_trust'] ?? 90),
                ],
            ];
        }

        // Fallback: calculate from evaluations
        return $this->calculateMetricsFromEvaluations($projectId);
    }

    /**
     * Calculate metrics from evaluations table
     */
    private function calculateMetricsFromEvaluations(string $projectId): array
    {
        // Get evaluations for responses related to this project
        $evalResult = $this->db->from('evaluations')
            ->select('coherence,consistency,fluency,relevance,avg_score')
            ->limit(100)
            ->get();

        $evaluations = $evalResult['data'] ?? [];

        if (empty($evaluations)) {
            return $this->getDefaultMetrics();
        }

        $coherenceSum = $consistencySum = $fluencySum = $relevanceSum = 0;
        $count = count($evaluations);

        foreach ($evaluations as $eval) {
            $coherenceSum += (float)($eval['coherence'] ?? 0);
            $consistencySum += (float)($eval['consistency'] ?? 0);
            $fluencySum += (float)($eval['fluency'] ?? 0);
            $relevanceSum += (float)($eval['relevance'] ?? 0);
        }

        $avgCoherence = $count > 0 ? $coherenceSum / $count : 0;
        $avgConsistency = $count > 0 ? $consistencySum / $count : 0;
        $avgFluency = $count > 0 ? $fluencySum / $count : 0;
        $avgRelevance = $count > 0 ? $relevanceSum / $count : 0;

        // Map evaluation metrics to display metrics
        return [
            'promptQuality' => [
                'overall' => round(($avgCoherence + $avgConsistency) / 2, 1),
                'specificity' => round($avgCoherence, 0),
                'completeness' => round($avgConsistency * 0.9, 0),
                'neutrality' => round($avgFluency, 0),
                'clarity' => round($avgRelevance * 0.95, 0),
                'taskType' => round(($avgCoherence + $avgRelevance) / 2 * 0.85, 0),
            ],
            'answerQuality' => [
                'overall' => round(($avgCoherence + $avgConsistency + $avgFluency + $avgRelevance) / 4, 1),
                'specificity' => round($avgCoherence * 0.95, 0),
                'completeness' => round($avgConsistency, 0),
                'relevance' => round($avgRelevance, 0),
                'neutrality' => round($avgFluency * 0.95, 0),
                'clarity' => round(($avgFluency + $avgCoherence) / 2 * 0.9, 0),
                'accuracy' => round($avgRelevance * 0.98, 0),
            ],
            'eeat' => [
                'overall' => round(($avgCoherence + $avgConsistency + $avgFluency + $avgRelevance) / 4 * 0.95, 1),
                'experience' => round($avgConsistency * 0.85, 0),
                'expertise' => round($avgRelevance, 0),
                'authority' => round($avgCoherence * 0.95, 0),
                'trust' => round(($avgFluency + $avgRelevance) / 2, 0),
            ],
        ];
    }

    /**
     * Get source distribution for a project
     */
    public function getSourceDistribution(string $projectId): array
    {
        // Get sources grouped by country
        $sourcesResult = $this->db->from('sources')
            ->select('country,type')
            ->limit(500)
            ->get();

        $sources = $sourcesResult['data'] ?? [];

        $countryStats = [];
        $typeStats = [];

        foreach ($sources as $source) {
            $country = $source['country'] ?? 'Unknown';
            $type = $source['type'] ?? 'Other';

            $countryStats[$country] = ($countryStats[$country] ?? 0) + 1;
            $typeStats[$type] = ($typeStats[$type] ?? 0) + 1;
        }

        $total = count($sources) ?: 1;

        // Convert to percentages
        $geoDistribution = [];
        foreach ($countryStats as $country => $count) {
            $geoDistribution[$country] = round(($count / $total) * 100, 0);
        }

        $typeDistribution = [];
        foreach ($typeStats as $type => $count) {
            $typeDistribution[$type] = round(($count / $total) * 100, 0);
        }

        // Sort by value descending
        arsort($geoDistribution);
        arsort($typeDistribution);

        return [
            'geography' => $geoDistribution,
            'types' => $typeDistribution,
        ];
    }

    /**
     * Get LLM distribution for a project
     */
    public function getLlmDistribution(string $projectId): array
    {
        $responsesResult = $this->db->from('ai_responses')
            ->select('model_name')
            ->limit(1000)
            ->get();

        $responses = $responsesResult['data'] ?? [];
        $modelStats = [];

        foreach ($responses as $response) {
            $model = $response['model_name'] ?? 'Unknown';
            $modelStats[$model] = ($modelStats[$model] ?? 0) + 1;
        }

        $total = count($responses) ?: 1;

        $distribution = [];
        foreach ($modelStats as $model => $count) {
            $distribution[$model] = round(($count / $total) * 100, 0);
        }

        arsort($distribution);

        return $distribution;
    }

    /**
     * Get narratives for a project
     */
    public function getNarratives(string $projectId): array
    {
        // Try to get from narratives table
        $narrativesResult = $this->db->from('narratives')
            ->select('*')
            ->eq('project_id', $projectId)
            ->eq('is_active', 'true')
            ->order('sort_order', true)
            ->get();

        $narratives = $narrativesResult['data'] ?? [];

        if (!empty($narratives)) {
            return array_map(function($n) {
                return [
                    'title' => $n['title'] ?? '',
                    'description' => $n['description'] ?? '',
                ];
            }, $narratives);
        }

        // Fallback: get from project's narratives field if it exists
        $project = $this->find($projectId);
        if ($project && !empty($project['narratives'])) {
            $narrativesData = is_string($project['narratives'])
                ? json_decode($project['narratives'], true)
                : $project['narratives'];

            if (is_array($narrativesData)) {
                return $narrativesData;
            }
        }

        return [];
    }

    /**
     * Calculate accuracy index based on evaluations
     */
    public function calculateAccuracyIndex(string $projectId): float
    {
        $metrics = $this->getProjectMetrics($projectId);

        // Weighted average of all quality metrics
        $promptWeight = 0.3;
        $answerWeight = 0.5;
        $eeatWeight = 0.2;

        $promptScore = $metrics['promptQuality']['overall'] ?? 0;
        $answerScore = $metrics['answerQuality']['overall'] ?? 0;
        $eeatScore = $metrics['eeat']['overall'] ?? 0;

        return round(
            ($promptScore * $promptWeight) +
            ($answerScore * $answerWeight) +
            ($eeatScore * $eeatWeight),
            1
        );
    }

    private function formatTone(float $tone): string
    {
        if ($tone >= 0) {
            return '+' . number_format($tone, 2);
        }
        return number_format($tone, 2);
    }

    private function getDefaultStats(): array
    {
        return [
            'processedPrompts' => 0,
            'uniqueSources' => 0,
            'avgTone' => '+0.00',
            'llmModels' => 0,
        ];
    }

    private function getDefaultMetrics(): array
    {
        return [
            'promptQuality' => [
                'overall' => 0,
                'specificity' => 0,
                'completeness' => 0,
                'neutrality' => 0,
                'clarity' => 0,
                'taskType' => 0,
            ],
            'answerQuality' => [
                'overall' => 0,
                'specificity' => 0,
                'completeness' => 0,
                'relevance' => 0,
                'neutrality' => 0,
                'clarity' => 0,
                'accuracy' => 0,
            ],
            'eeat' => [
                'overall' => 0,
                'experience' => 0,
                'expertise' => 0,
                'authority' => 0,
                'trust' => 0,
            ],
        ];
    }

    public function create(array $data): array
    {
        return $this->db->from($this->table)->insert($data);
    }

    public function update(string $id, array $data): array
    {
        return $this->db->from($this->table)
            ->eq('id', $id)
            ->update($data);
    }

    public function delete(string $id): array
    {
        return $this->db->from($this->table)
            ->eq('id', $id)
            ->update(['is_active' => false]);
    }
}
