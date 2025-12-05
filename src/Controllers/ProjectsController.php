<?php

namespace App\Controllers;

use App\Models\Project;
use App\Models\AiResponse;
use App\Models\Source;
use App\Services\SupabaseClient;

class ProjectsController extends BaseController
{
    public function index(): void
    {
        $projectModel = new Project();
        $result = $projectModel->all();

        $projects = [];
        if (isset($result['data']) && is_array($result['data'])) {
            foreach ($result['data'] as $p) {
                $projects[] = [
                    'id' => $p['id'],
                    'title' => $p['name'],
                    'description' => $p['description'],
                    'icon' => $p['icon'],
                    'type' => $p['type'],
                    'badge' => $p['badge'],
                    'accuracy' => (float)$p['accuracy_score'],
                    'trend' => ($p['trend_direction'] === 'up' ? '+' : '-') . abs($p['trend_percent']) . '%',
                    'trendUp' => $p['trend_direction'] === 'up',
                ];
            }
        }

        $this->render('projects/index', [
            'pageTitle' => 'Проекты и направления',
            'currentPage' => 'projects',
            'breadcrumb' => 'Проекты',
            'projects' => $projects,
            'projectCount' => count($projects),
        ]);
    }

    public function show(string $id): void
    {
        $projectModel = new Project();
        $project = $projectModel->find($id);

        if (!$project) {
            http_response_code(404);
            include __DIR__ . '/../Views/errors/404.php';
            return;
        }

        $db = new SupabaseClient();

        // Get real project stats from database
        $stats = $this->getProjectStats($db, $id);

        // Get narratives for this project from database
        $narratives = $this->getProjectNarratives($db, $id);

        // Get recent responses for this project
        $responsesResult = $db->from('ai_responses')
            ->select('*')
            ->eq('project_id', $id)
            ->order('created_at', false)
            ->limit(10)
            ->get();

        // Get evaluations for this project
        $evaluationsResult = $db->from('recent_evaluations_detailed')
            ->select('*')
            ->eq('project_id', $id)
            ->order('evaluated_at', false)
            ->limit(10)
            ->get();

        $this->render('projects/show', [
            'pageTitle' => $project['name'],
            'currentPage' => 'projects',
            'project' => $project,
            'stats' => $stats,
            'narratives' => $narratives,
            'recentResponses' => $responsesResult['data'] ?? [],
            'recentEvaluations' => $evaluationsResult['data'] ?? [],
        ]);
    }

    public function create(): void
    {
        $this->render('projects/create', [
            'pageTitle' => 'Создать проект',
            'currentPage' => 'projects',
            'breadcrumb' => 'Создание проекта',
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

        if (empty($data['name'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Project name is required']);
            return;
        }

        $projectModel = new Project();
        $result = $projectModel->create([
            'name' => $data['name'],
            'description' => $data['description'] ?? '',
            'icon' => $data['icon'] ?? '📊',
            'type' => $data['type'] ?? 'private',
            'badge' => $data['badge'] ?? ($data['type'] === 'gov' ? 'Гос. партнёр' : 'Частный партнёр'),
            'accuracy_score' => 0,
            'trend_direction' => 'up',
            'trend_percent' => 0,
            'is_active' => true,
        ]);

        if ($result['status'] >= 200 && $result['status'] < 300) {
            http_response_code(201);
            echo json_encode(['success' => true, 'data' => $result['data']]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to create project']);
        }
    }

    public function update(string $id): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'PUT' && $_SERVER['REQUEST_METHOD'] !== 'PATCH') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);

        $projectModel = new Project();
        $project = $projectModel->find($id);

        if (!$project) {
            http_response_code(404);
            echo json_encode(['error' => 'Project not found']);
            return;
        }

        $updateData = [];
        if (isset($data['name'])) $updateData['name'] = $data['name'];
        if (isset($data['description'])) $updateData['description'] = $data['description'];
        if (isset($data['icon'])) $updateData['icon'] = $data['icon'];
        if (isset($data['type'])) $updateData['type'] = $data['type'];
        if (isset($data['badge'])) $updateData['badge'] = $data['badge'];
        if (isset($data['accuracy_score'])) $updateData['accuracy_score'] = $data['accuracy_score'];
        if (isset($data['is_active'])) $updateData['is_active'] = $data['is_active'];

        $updateData['updated_at'] = date('c');

        $result = $projectModel->update($id, $updateData);

        if ($result['status'] >= 200 && $result['status'] < 300) {
            echo json_encode(['success' => true, 'data' => $result['data']]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to update project']);
        }
    }

    public function destroy(string $id): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }

        $projectModel = new Project();
        $result = $projectModel->delete($id); // Soft delete (sets is_active to false)

        if ($result['status'] >= 200 && $result['status'] < 300) {
            echo json_encode(['success' => true]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to delete project']);
        }
    }

    private function getProjectStats(SupabaseClient $db, string $projectId): array
    {
        // Get stats from project_stats table
        $statsResult = $db->from('project_stats')
            ->select('*')
            ->eq('project_id', $projectId)
            ->single();

        if ($statsResult) {
            return [
                'processedPrompts' => (int)($statsResult['processed_prompts'] ?? 0),
                'uniqueSources' => (int)($statsResult['unique_sources'] ?? 0),
                'avgTone' => $this->formatTone((float)($statsResult['avg_tone'] ?? 0)),
                'llmModels' => (int)($statsResult['llm_models'] ?? 0),
            ];
        }

        // If no stats record, calculate from responses
        $responsesResult = $db->from('ai_responses')
            ->select('*')
            ->eq('project_id', $projectId)
            ->get();

        $responses = $responsesResult['data'] ?? [];
        $promptCount = count($responses);

        // Count unique models
        $models = [];
        $toneSum = 0;
        foreach ($responses as $resp) {
            $models[$resp['model_name']] = true;
            $toneSum += $this->toneToNumber($resp['tone'] ?? 'neutral');
        }

        $avgTone = $promptCount > 0 ? $toneSum / $promptCount : 0;

        return [
            'processedPrompts' => $promptCount,
            'uniqueSources' => 0, // Would need to count from sources linked to responses
            'avgTone' => $this->formatTone($avgTone),
            'llmModels' => count($models),
        ];
    }

    private function getProjectNarratives(SupabaseClient $db, string $projectId): array
    {
        $result = $db->from('project_narratives')
            ->select('*')
            ->eq('project_id', $projectId)
            ->order('order_index', true)
            ->get();

        if (isset($result['data']) && !empty($result['data'])) {
            return array_map(function($n) {
                return [
                    'id' => $n['id'],
                    'title' => $n['title'],
                    'description' => $n['description'],
                ];
            }, $result['data']);
        }

        // Return empty array if no narratives
        return [];
    }

    private function toneToNumber(string $tone): float
    {
        switch ($tone) {
            case 'positive': return 1;
            case 'negative': return -1;
            default: return 0;
        }
    }

    private function formatTone(float $value): string
    {
        if ($value > 0) {
            return '+' . number_format($value, 2);
        } elseif ($value < 0) {
            return number_format($value, 2);
        }
        return '0.00';
    }
}
