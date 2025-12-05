<?php

namespace App\Models;

use App\Services\SupabaseClient;

class PromptSet
{
    private SupabaseClient $db;
    private string $table = 'prompt_sets';

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

    public function byName(string $name): ?array
    {
        return $this->db->from($this->table)
            ->select('*')
            ->eq('name', $name)
            ->single();
    }

    public function search(string $query): array
    {
        return $this->db->from($this->table)
            ->select('*')
            ->ilike('name', '%' . $query . '%')
            ->order('created_at', false)
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
