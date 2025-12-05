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
