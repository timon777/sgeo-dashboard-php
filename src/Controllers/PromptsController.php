<?php

namespace App\Controllers;

use App\Models\AiResponse;
use App\Models\Evaluation;
use App\Models\PromptSet;
use App\Models\Project;
use App\Services\Cache;

class PromptsController extends BaseController
{
    public function index(): void
    {
        $evaluationModel = new Evaluation();

        // Pagination parameters
        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 20;
        $offset = ($page - 1) * $perPage;

        // Get total count from cache
        $totalPrompts = Cache::remember('prompts_total_count', function() {
            $aiResponseModel = new AiResponse();
            $stats = $aiResponseModel->getStats();
            return $stats['total'] ?? 0;
        }, 300);

        $totalPages = max(1, ceil($totalPrompts / $perPage));

        // Get evaluations with pagination (main data source)
        $evaluationsResult = $evaluationModel->recentDetailed($perPage, $offset);

        // Format prompts for display - no full_response in main load
        $prompts = [];
        if (isset($evaluationsResult['data']) && is_array($evaluationsResult['data'])) {
            foreach ($evaluationsResult['data'] as $eval) {
                $prompts[] = [
                    'id' => $eval['ai_response_id'],
                    'prompt' => $this->truncateText($eval['prompt'] ?? '', 200),
                    'full_prompt' => $eval['prompt'] ?? '',
                    'llm' => $this->mapModelToShortName($eval['model_name'] ?? ''),
                    'response' => $this->truncateText($eval['response'] ?? '', 100),
                    'tone' => $eval['tone'] ?? 'neutral',
                    'score' => (int)($eval['avg_score'] ?? 0),
                    'risk' => $eval['risk_level'] ?? 'low',
                    'date' => $this->formatDate($eval['evaluated_at'] ?? ''),
                ];
            }
        }

        // Fallback to ai_responses if no evaluations
        if (empty($prompts)) {
            $aiResponseModel = new AiResponse();
            $responsesResult = $aiResponseModel->all($perPage, $offset);
            if (isset($responsesResult['data']) && is_array($responsesResult['data'])) {
                foreach ($responsesResult['data'] as $resp) {
                    $prompts[] = [
                        'id' => $resp['id'],
                        'prompt' => $this->truncateText($resp['prompt'] ?? '', 200),
                        'full_prompt' => $resp['prompt'] ?? '',
                        'llm' => $this->mapModelToShortName($resp['model_name'] ?? ''),
                        'response' => $this->truncateText($resp['response'] ?? '', 100),
                        'tone' => $resp['tone'] ?? 'neutral',
                        'score' => 0,
                        'risk' => $resp['risk_level'] ?? 'low',
                        'date' => $this->formatDate($resp['created_at'] ?? ''),
                    ];
                }
            }
        }

        $this->render('prompts/index', [
            'pageTitle' => 'Промты и ответы LLM',
            'currentPage' => 'prompts',
            'breadcrumb' => 'Промты',
            'prompts' => $prompts,
            'totalPrompts' => $totalPrompts,
            'paginationPage' => $page,
            'totalPages' => $totalPages,
            'perPage' => $perPage,
        ]);
    }

    // AJAX endpoint for getting full prompt details
    public function getDetail(string $id): void
    {
        header('Content-Type: application/json');

        $evaluationModel = new Evaluation();
        $aiResponseModel = new AiResponse();

        // Try to get from evaluations first
        $response = $aiResponseModel->find($id);

        if (!$response) {
            http_response_code(404);
            echo json_encode(['error' => 'Not found']);
            return;
        }

        echo json_encode([
            'id' => $response['id'],
            'prompt' => $response['prompt'] ?? '',
            'response' => $response['response'] ?? '',
            'llm' => $this->mapModelToShortName($response['model_name'] ?? ''),
            'tone' => $response['tone'] ?? 'neutral',
            'risk' => $response['risk_level'] ?? 'low',
            'date' => $this->formatDate($response['created_at'] ?? ''),
        ]);
    }

    public function store(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['prompt']) || empty($data['model_name'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Prompt and model_name are required']);
            return;
        }

        $aiResponseModel = new AiResponse();
        $result = $aiResponseModel->create([
            'prompt' => $data['prompt'],
            'response' => $data['response'] ?? null,
            'model_name' => $data['model_name'],
            'language' => $data['language'] ?? 'ru',
            'project_id' => $data['project_id'] ?? null,
            'tone' => $data['tone'] ?? 'neutral',
            'risk_level' => $data['risk_level'] ?? 'low',
        ]);

        if ($result['status'] >= 200 && $result['status'] < 300) {
            http_response_code(201);
            echo json_encode(['success' => true, 'data' => $result['data']]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to create prompt']);
        }
    }

    public function show(string $id): void
    {
        $aiResponseModel = new AiResponse();
        $evaluationModel = new Evaluation();

        $response = $aiResponseModel->find($id);

        if (!$response) {
            http_response_code(404);
            include __DIR__ . '/../Views/errors/404.php';
            return;
        }

        $evaluations = $evaluationModel->byAiResponse($id);

        $this->render('prompts/show', [
            'pageTitle' => 'Детали промпта',
            'currentPage' => 'prompts',
            'response' => $response,
            'evaluations' => $evaluations['data'] ?? [],
        ]);
    }

    public function delete(string $id): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }

        $aiResponseModel = new AiResponse();
        $result = $aiResponseModel->delete($id);

        if ($result['status'] >= 200 && $result['status'] < 300) {
            echo json_encode(['success' => true]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to delete prompt']);
        }
    }

    private function mapModelToShortName(string $modelName): string
    {
        $map = [
            'ChatGPT' => 'gpt',
            'GPT-4' => 'gpt',
            'GPT-3.5' => 'gpt',
            'DeepSeek' => 'deepseek',
            'Grok' => 'grok',
            'Gemini' => 'gemini',
            'Perplexity' => 'perplexity',
            'Claude' => 'claude',
            'Copilot' => 'copilot',
        ];

        return $map[$modelName] ?? strtolower($modelName);
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
        return date('Y-m-d', strtotime($date));
    }

    public function exportCsv(): void
    {
        $evaluationModel = new Evaluation();
        $result = $evaluationModel->recentDetailed(1000);

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="prompts_' . date('Y-m-d') . '.csv"');

        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        fputcsv($output, ['ID', 'Промт', 'LLM', 'Ответ', 'Тональность', 'Балл', 'Риск', 'Проект', 'Дата']);

        if (isset($result['data'])) {
            foreach ($result['data'] as $eval) {
                fputcsv($output, [
                    $eval['ai_response_id'] ?? '',
                    $eval['prompt'] ?? '',
                    $eval['model_name'] ?? '',
                    mb_substr($eval['response'] ?? '', 0, 500),
                    $eval['tone'] ?? '',
                    $eval['avg_score'] ?? 0,
                    $eval['risk_level'] ?? '',
                    $eval['project_name'] ?? '',
                    $eval['evaluated_at'] ?? ''
                ]);
            }
        }

        fclose($output);
    }
}
