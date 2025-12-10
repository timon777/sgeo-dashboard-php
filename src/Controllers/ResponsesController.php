<?php

namespace App\Controllers;

use App\Services\SupabaseClient;
use App\Services\Cache;

class ResponsesController extends BaseController
{
    private SupabaseClient $db;

    public function __construct()
    {
        $this->db = new SupabaseClient();
    }

    public function index(): void
    {
        // Clear cache if requested via URL parameter
        if (isset($_GET['clear_cache']) && $_GET['clear_cache'] === '1') {
            Cache::flush();
        }

        // Get initial responses (first page)
        $responses = $this->getResponses(0, 20);

        $this->render('responses/index', [
            'pageTitle' => 'Ответы LLM',
            'currentPage' => 'responses',
            'breadcrumb' => 'Ответы',
            'responses' => $responses['responses'],
            'totalCount' => $responses['totalCount'],
            'hasMore' => $responses['hasMore'],
        ]);
    }

    public function apiList(): void
    {
        header('Content-Type: application/json');

        $offset = (int)($_GET['offset'] ?? 0);
        $limit = (int)($_GET['limit'] ?? 20);

        $responses = $this->getResponses($offset, $limit);

        echo json_encode([
            'success' => true,
            'data' => $responses,
        ]);
    }

    private function getResponses(int $offset = 0, int $limit = 20): array
    {
        // Get responses with evaluations
        $result = $this->db->from('evaluations')
            ->select('*, ai_responses!inner(id, prompt, response, model_name, project_id, created_at, projects(name))')
            ->order('evaluated_at', false)
            ->offset($offset)
            ->limit($limit)
            ->get();

        $responses = [];
        foreach ($result['data'] ?? [] as $r) {
            $aiResponse = $r['ai_responses'] ?? [];
            $project = $aiResponse['projects'] ?? [];

            // G-EVAL individual scores are 1-5 scale, multiply by 20 to get percentage
            $coherence = (int)(($r['coherence'] ?? 0) * 20);
            $consistency = (int)(($r['consistency'] ?? 0) * 20);
            $fluency = (int)(($r['fluency'] ?? 0) * 20);
            $relevance = (int)(($r['relevance'] ?? 0) * 20);
            $avgScore = (int)($r['avg_score'] ?? 0);

            $responses[] = [
                'id' => $r['ai_response_id'],
                'prompt' => $this->truncateText($aiResponse['prompt'] ?? '', 150),
                'promptFull' => $aiResponse['prompt'] ?? '',
                'response' => $this->truncateText($aiResponse['response'] ?? '', 250),
                'responseFull' => $aiResponse['response'] ?? '',
                'model' => $aiResponse['model_name'] ?? '',
                'projectName' => $project['name'] ?? 'Без проекта',
                'projectId' => $aiResponse['project_id'] ?? '',
                'coherence' => $coherence,
                'consistency' => $consistency,
                'fluency' => $fluency,
                'relevance' => $relevance,
                'avgScore' => $avgScore,
                'tone' => $r['tone'] ?? 'neutral',
                'date' => $this->formatDate($r['evaluated_at'] ?? $aiResponse['created_at'] ?? ''),
            ];
        }

        // Get total count (cached)
        $totalCount = Cache::remember('responses_total_count', function() {
            $countResult = $this->db->from('evaluations')
                ->select('id')
                ->get();
            return count($countResult['data'] ?? []);
        }, 300);

        return [
            'responses' => $responses,
            'totalCount' => $totalCount,
            'hasMore' => ($offset + $limit) < $totalCount,
        ];
    }

    private function truncateText(string $text, int $length): string
    {
        if (mb_strlen($text) <= $length) {
            return $text;
        }
        return mb_substr($text, 0, $length) . '...';
    }

    private function formatDate(string $date): string
    {
        if (empty($date)) {
            return '';
        }
        return date('d.m.Y', strtotime($date));
    }

    public function exportCsv(): void
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="responses_' . date('Y-m-d') . '.csv"');

        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM

        // Headers
        fputcsv($output, ['Модель', 'Проект', 'Промт', 'Ответ', 'Связность', 'Согласованность', 'Беглость', 'Релевантность', 'Среднее', 'Тональность', 'Дата']);

        // Get all responses (no pagination for export)
        $result = $this->db->from('evaluations')
            ->select('*, ai_responses!inner(prompt, response, model_name, project_id, created_at, projects(name))')
            ->order('evaluated_at', false)
            ->get();

        foreach ($result['data'] ?? [] as $r) {
            $aiResponse = $r['ai_responses'] ?? [];
            $project = $aiResponse['projects'] ?? [];

            fputcsv($output, [
                $aiResponse['model_name'] ?? '',
                $project['name'] ?? '',
                $aiResponse['prompt'] ?? '',
                $aiResponse['response'] ?? '',
                (int)(($r['coherence'] ?? 0) * 20) . '%',
                (int)(($r['consistency'] ?? 0) * 20) . '%',
                (int)(($r['fluency'] ?? 0) * 20) . '%',
                (int)(($r['relevance'] ?? 0) * 20) . '%',
                (int)($r['avg_score'] ?? 0) . '%',
                $r['tone'] ?? '',
                $this->formatDate($r['evaluated_at'] ?? $aiResponse['created_at'] ?? ''),
            ]);
        }

        fclose($output);
        exit;
    }
}
