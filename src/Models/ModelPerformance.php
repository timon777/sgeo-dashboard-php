<?php

namespace App\Models;

use App\Services\SupabaseClient;

class ModelPerformance
{
    private SupabaseClient $db;
    private string $table = 'model_performance_summary';

    public function __construct()
    {
        $this->db = new SupabaseClient();
    }

    public function all(): array
    {
        return $this->db->from($this->table)
            ->select('*')
            ->order('overall_avg_score', false)
            ->get();
    }

    public function byModel(string $modelName): ?array
    {
        return $this->db->from($this->table)
            ->select('*')
            ->eq('model_name', $modelName)
            ->single();
    }

    public function topPerformers(int $limit = 5): array
    {
        return $this->db->from($this->table)
            ->select('*')
            ->order('overall_avg_score', false)
            ->limit($limit)
            ->get();
    }

    public function getMetricsComparison(): array
    {
        $result = $this->all();

        if (!isset($result['data'])) {
            return [];
        }

        $models = [];
        foreach ($result['data'] as $model) {
            $models[$model['model_name']] = [
                'coherence' => (float)($model['avg_coherence'] ?? 0),
                'consistency' => (float)($model['avg_consistency'] ?? 0),
                'fluency' => (float)($model['avg_fluency'] ?? 0),
                'relevance' => (float)($model['avg_relevance'] ?? 0),
                'overall' => (float)($model['overall_avg_score'] ?? 0),
                'total_evaluations' => (int)($model['total_evaluations'] ?? 0),
            ];
        }

        return $models;
    }
}
