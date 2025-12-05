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
                    'description' => $p['description'] ?? '',
                    'icon' => $p['icon'] ?? '',
                    'type' => $p['type'] ?? 'general',
                    'badge' => $p['badge'] ?? '',
                    'accuracy' => (float)($p['accuracy_score'] ?? 0),
                    'trend' => (($p['trend_direction'] ?? 'up') === 'up' ? '+' : '-') . abs($p['trend_percent'] ?? 0) . '%',
                    'trendUp' => ($p['trend_direction'] ?? 'up') === 'up',
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

        // Get project-related stats from database
        $db = new SupabaseClient();

        // Get prompts count for this project
        $promptsResult = $db->from('ai_responses')
            ->select('count')
            ->eq('project_id', $id)
            ->get();
        $promptsCount = $promptsResult['data'][0]['count'] ?? 0;

        // Get sources count for this project
        $sourcesResult = $db->from('project_sources')
            ->select('count')
            ->eq('project_id', $id)
            ->get();
        $sourcesCount = $sourcesResult['data'][0]['count'] ?? 0;

        // Get narratives for this project
        $narrativesResult = $db->from('project_narratives')
            ->select('*')
            ->eq('project_id', $id)
            ->eq('is_active', 'true')
            ->order('priority', true)
            ->get();

        $narratives = [];
        if (isset($narrativesResult['data']) && is_array($narrativesResult['data'])) {
            foreach ($narrativesResult['data'] as $n) {
                $narratives[] = [
                    'title' => $n['title'] ?? '',
                    'description' => $n['description'] ?? '',
                ];
            }
        }

        $stats = [
            'processedPrompts' => $promptsCount,
            'uniqueSources' => $sourcesCount,
            'avgTone' => $project['avg_tone'] ?? '0.00',
            'llmModels' => $project['llm_models_count'] ?? 0,
        ];

        $this->render('projects/show', [
            'pageTitle' => $project['name'],
            'currentPage' => 'projects',
            'project' => $project,
            'stats' => $stats,
            'narratives' => $narratives,
        ]);
    }
}
