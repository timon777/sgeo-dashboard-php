<?php

namespace App\Services;

class SupabaseClient
{
    private string $url;
    private string $key;
    private string $serviceKey;

    public function __construct()
    {
        $config = require __DIR__ . '/../../config/database.php';
        $this->url = rtrim($config['supabase_url'], '/');
        $this->key = $config['supabase_key'];
        $this->serviceKey = $config['supabase_service_key'];
    }

    /**
     * Make a REST API request to Supabase
     */
    public function request(string $method, string $endpoint, array $data = [], bool $useServiceKey = false): array
    {
        $url = $this->url . '/rest/v1/' . ltrim($endpoint, '/');
        $key = $useServiceKey ? $this->serviceKey : $this->key;

        $headers = [
            'apikey: ' . $key,
            'Authorization: Bearer ' . $key,
            'Content-Type: application/json',
            'Prefer: return=representation',
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        switch (strtoupper($method)) {
            case 'POST':
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                break;
            case 'PATCH':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                break;
            case 'DELETE':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
                break;
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            return ['error' => $error, 'status' => 0];
        }

        return [
            'data' => json_decode($response, true),
            'status' => $httpCode,
        ];
    }

    /**
     * Query a table with optional filters
     */
    public function from(string $table): QueryBuilder
    {
        return new QueryBuilder($this, $table);
    }

    /**
     * Execute raw query via RPC
     */
    public function rpc(string $function, array $params = []): array
    {
        $url = $this->url . '/rest/v1/rpc/' . $function;

        $headers = [
            'apikey: ' . $this->key,
            'Authorization: Bearer ' . $this->key,
            'Content-Type: application/json',
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return [
            'data' => json_decode($response, true),
            'status' => $httpCode,
        ];
    }
}

class QueryBuilder
{
    private SupabaseClient $client;
    private string $table;
    private array $filters = [];
    private ?string $select = null;
    private ?string $order = null;
    private ?int $limit = null;
    private ?int $offset = null;

    public function __construct(SupabaseClient $client, string $table)
    {
        $this->client = $client;
        $this->table = $table;
    }

    public function select(string $columns = '*'): self
    {
        $this->select = $columns;
        return $this;
    }

    public function eq(string $column, $value): self
    {
        $this->filters[] = $column . '=eq.' . urlencode($value);
        return $this;
    }

    public function neq(string $column, $value): self
    {
        $this->filters[] = $column . '=neq.' . urlencode($value);
        return $this;
    }

    public function gt(string $column, $value): self
    {
        $this->filters[] = $column . '=gt.' . urlencode($value);
        return $this;
    }

    public function gte(string $column, $value): self
    {
        $this->filters[] = $column . '=gte.' . urlencode($value);
        return $this;
    }

    public function lt(string $column, $value): self
    {
        $this->filters[] = $column . '=lt.' . urlencode($value);
        return $this;
    }

    public function lte(string $column, $value): self
    {
        $this->filters[] = $column . '=lte.' . urlencode($value);
        return $this;
    }

    public function like(string $column, string $pattern): self
    {
        $this->filters[] = $column . '=like.' . urlencode($pattern);
        return $this;
    }

    public function ilike(string $column, string $pattern): self
    {
        $this->filters[] = $column . '=ilike.' . urlencode($pattern);
        return $this;
    }

    public function in(string $column, array $values): self
    {
        $this->filters[] = $column . '=in.(' . implode(',', array_map('urlencode', $values)) . ')';
        return $this;
    }

    public function order(string $column, bool $ascending = true): self
    {
        $this->order = $column . '.' . ($ascending ? 'asc' : 'desc');
        return $this;
    }

    public function limit(int $limit): self
    {
        $this->limit = $limit;
        return $this;
    }

    public function offset(int $offset): self
    {
        $this->offset = $offset;
        return $this;
    }

    public function get(): array
    {
        $endpoint = $this->table;
        $params = [];

        if ($this->select) {
            $params['select'] = $this->select;
        }

        if ($this->order) {
            $params['order'] = $this->order;
        }

        if ($this->limit !== null) {
            $params['limit'] = $this->limit;
        }

        if ($this->offset !== null) {
            $params['offset'] = $this->offset;
        }

        $queryString = http_build_query($params);
        if (!empty($this->filters)) {
            $queryString .= ($queryString ? '&' : '') . implode('&', $this->filters);
        }

        if ($queryString) {
            $endpoint .= '?' . $queryString;
        }

        return $this->client->request('GET', $endpoint);
    }

    public function insert(array $data): array
    {
        return $this->client->request('POST', $this->table, $data);
    }

    public function update(array $data): array
    {
        $endpoint = $this->table;
        if (!empty($this->filters)) {
            $endpoint .= '?' . implode('&', $this->filters);
        }
        return $this->client->request('PATCH', $endpoint, $data);
    }

    public function delete(): array
    {
        $endpoint = $this->table;
        if (!empty($this->filters)) {
            $endpoint .= '?' . implode('&', $this->filters);
        }
        return $this->client->request('DELETE', $endpoint);
    }

    public function single(): ?array
    {
        $this->limit(1);
        $result = $this->get();
        if (isset($result['data']) && is_array($result['data']) && count($result['data']) > 0) {
            return $result['data'][0];
        }
        return null;
    }

    public function count(): int
    {
        $endpoint = $this->table . '?select=count';
        if (!empty($this->filters)) {
            $endpoint .= '&' . implode('&', $this->filters);
        }
        $result = $this->client->request('GET', $endpoint);
        return $result['data'][0]['count'] ?? 0;
    }
}
