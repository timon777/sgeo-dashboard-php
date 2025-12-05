<?php

namespace App\Models;

use App\Services\SupabaseClient;

class Setting
{
    private SupabaseClient $db;
    private string $table = 'settings';

    public function __construct()
    {
        $this->db = new SupabaseClient();
    }

    public function all(): array
    {
        return $this->db->from($this->table)
            ->select('*')
            ->get();
    }

    public function get(string $key, $default = null)
    {
        $result = $this->db->from($this->table)
            ->select('value')
            ->eq('key', $key)
            ->single();

        if ($result && isset($result['value'])) {
            return json_decode($result['value'], true) ?? $result['value'];
        }

        return $default;
    }

    public function set(string $key, $value, ?string $description = null): array
    {
        $existing = $this->db->from($this->table)
            ->select('id')
            ->eq('key', $key)
            ->single();

        $data = ['value' => json_encode($value)];
        if ($description !== null) {
            $data['description'] = $description;
        }

        if ($existing) {
            return $this->db->from($this->table)
                ->eq('key', $key)
                ->update($data);
        }

        $data['key'] = $key;
        return $this->db->from($this->table)->insert($data);
    }

    public function delete(string $key): array
    {
        return $this->db->from($this->table)
            ->eq('key', $key)
            ->delete();
    }

    public function getApiKeys(): array
    {
        return $this->db->from('api_keys')
            ->select('id,provider,name,key_hint,status,last_used_at')
            ->order('provider', true)
            ->get();
    }

    public function createApiKey(array $data): array
    {
        return $this->db->from('api_keys')->insert($data);
    }

    public function updateApiKey(string $id, array $data): array
    {
        return $this->db->from('api_keys')
            ->eq('id', $id)
            ->update($data);
    }

    public function deleteApiKey(string $id): array
    {
        return $this->db->from('api_keys')
            ->eq('id', $id)
            ->delete();
    }
}
