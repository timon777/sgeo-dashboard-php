<?php

namespace App\Models;

use App\Services\SupabaseClient;

class Report
{
    private SupabaseClient $db;
    private string $table = 'reports';

    public function __construct()
    {
        $this->db = new SupabaseClient();
    }

    public function all(int $limit = 50): array
    {
        return $this->db->from($this->table)
            ->select('*')
            ->order('created_at', false)
            ->limit($limit)
            ->get();
    }

    public function find(string $id): ?array
    {
        return $this->db->from($this->table)
            ->select('*')
            ->eq('id', $id)
            ->single();
    }

    public function byType(string $type, int $limit = 20): array
    {
        return $this->db->from($this->table)
            ->select('*')
            ->eq('type', $type)
            ->order('created_at', false)
            ->limit($limit)
            ->get();
    }

    public function completed(int $limit = 20): array
    {
        return $this->db->from($this->table)
            ->select('*')
            ->eq('status', 'completed')
            ->order('generated_at', false)
            ->limit($limit)
            ->get();
    }

    public function getTemplates(): array
    {
        return $this->db->from('report_templates')
            ->select('*')
            ->eq('is_active', 'true')
            ->get();
    }

    public function create(array $data): array
    {
        return $this->db->from($this->table)->insert($data);
    }

    public function updateStatus(string $id, string $status): array
    {
        $data = ['status' => $status];
        if ($status === 'completed') {
            $data['generated_at'] = date('c');
        }
        return $this->db->from($this->table)
            ->eq('id', $id)
            ->update($data);
    }
}
