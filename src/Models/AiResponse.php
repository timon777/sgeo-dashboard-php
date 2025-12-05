<?php

namespace App\Models;

use App\Services\SupabaseClient;

class AiResponse
{
    private SupabaseClient $db;
    private string $table = 'ai_responses';

    public function __construct()
    {
        $this->db = new SupabaseClient();
    }

    public function all(int $limit = 50, int $offset = 0): array
    {
        return $this->db->from($this->table)
            ->select('*')
            ->order('created_at', false)
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

    public function byModel(string $modelName, int $limit = 50): array
    {
        return $this->db->from($this->table)
            ->select('*')
            ->eq('model_name', $modelName)
            ->order('created_at', false)
            ->limit($limit)
            ->get();
    }

    public function byLanguage(string $language, int $limit = 50): array
    {
        return $this->db->from($this->table)
            ->select('*')
            ->eq('language', $language)
            ->order('created_at', false)
            ->limit($limit)
            ->get();
    }

    public function search(string $query, int $limit = 50): array
    {
        return $this->db->from($this->table)
            ->select('*')
            ->ilike('prompt', '%' . $query . '%')
            ->order('created_at', false)
            ->limit($limit)
            ->get();
    }

    public function getStats(): array
    {
        $total = $this->db->from($this->table)->count();

        return [
            'total' => $total,
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
            ->delete();
    }
}
