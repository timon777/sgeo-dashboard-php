<?php

namespace App\Models;

use App\Services\SupabaseClient;

class Source
{
    private SupabaseClient $db;
    private string $table = 'sources';

    public function __construct()
    {
        $this->db = new SupabaseClient();
    }

    public function all(int $limit = 50, int $offset = 0): array
    {
        return $this->db->from($this->table)
            ->select('*')
            ->order('eeat_combined', false)
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

    public function byDomain(string $domain): ?array
    {
        return $this->db->from($this->table)
            ->select('*')
            ->eq('domain', $domain)
            ->single();
    }

    public function byType(string $type, int $limit = 50): array
    {
        return $this->db->from($this->table)
            ->select('*')
            ->eq('type', $type)
            ->order('eeat_combined', false)
            ->limit($limit)
            ->get();
    }

    public function byCountry(string $country, int $limit = 50): array
    {
        return $this->db->from($this->table)
            ->select('*')
            ->eq('country', $country)
            ->order('eeat_combined', false)
            ->limit($limit)
            ->get();
    }

    public function highQuality(int $minEeat = 80, int $limit = 50): array
    {
        return $this->db->from($this->table)
            ->select('*')
            ->gte('eeat_combined', $minEeat)
            ->order('eeat_combined', false)
            ->limit($limit)
            ->get();
    }

    public function getStats(): array
    {
        return $this->db->from('sources_stats')
            ->select('*')
            ->get();
    }

    public function count(): int
    {
        $result = $this->all(1000, 0);
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
            ->delete();
    }
}
