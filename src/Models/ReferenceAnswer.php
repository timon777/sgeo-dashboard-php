<?php

namespace App\Models;

use App\Services\SupabaseClient;

class ReferenceAnswer
{
    private SupabaseClient $db;
    private string $table = 'reference_answers';

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

    public function active(int $limit = 50): array
    {
        return $this->db->from($this->table)
            ->select('*')
            ->eq('is_active', 'true')
            ->order('created_at', false)
            ->limit($limit)
            ->get();
    }

    public function byTopic(string $topic, int $limit = 50): array
    {
        return $this->db->from($this->table)
            ->select('*')
            ->eq('topic', $topic)
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

    public function search(string $query): array
    {
        return $this->db->from($this->table)
            ->select('*')
            ->ilike('prompt_pattern', '%' . $query . '%')
            ->order('created_at', false)
            ->get();
    }

    public function getStats(): array
    {
        return $this->db->from('reference_stats')
            ->select('*')
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
