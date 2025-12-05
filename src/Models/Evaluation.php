<?php

namespace App\Models;

use App\Services\SupabaseClient;

class Evaluation
{
    private SupabaseClient $db;
    private string $table = 'evaluations';

    public function __construct()
    {
        $this->db = new SupabaseClient();
    }

    public function all(int $limit = 50, int $offset = 0): array
    {
        return $this->db->from($this->table)
            ->select('*')
            ->order('evaluated_at', false)
            ->limit($limit)
            ->offset($offset)
            ->get();
    }

    public function find(string $id): ?array
    {
        return $this->db->from($this->table)
            ->select('*')
            ->eq('id', $id)
            ->single();
    }

    public function byAiResponse(string $aiResponseId): array
    {
        return $this->db->from($this->table)
            ->select('*')
            ->eq('ai_response_id', $aiResponseId)
            ->order('evaluated_at', false)
            ->get();
    }

    public function byEvaluator(string $evaluatorModel, int $limit = 50): array
    {
        return $this->db->from($this->table)
            ->select('*')
            ->eq('evaluator_model', $evaluatorModel)
            ->order('evaluated_at', false)
            ->limit($limit)
            ->get();
    }

    public function highScoring(int $minScore = 80, int $limit = 50): array
    {
        return $this->db->from($this->table)
            ->select('*')
            ->gte('avg_score', $minScore)
            ->order('avg_score', false)
            ->limit($limit)
            ->get();
    }

    public function lowScoring(int $maxScore = 50, int $limit = 50): array
    {
        return $this->db->from($this->table)
            ->select('*')
            ->lte('avg_score', $maxScore)
            ->order('avg_score', true)
            ->limit($limit)
            ->get();
    }

    public function recentDetailed(int $limit = 50): array
    {
        return $this->db->from('recent_evaluations_detailed')
            ->select('*')
            ->order('evaluated_at', false)
            ->limit($limit)
            ->get();
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
            ->delete();
    }
}
