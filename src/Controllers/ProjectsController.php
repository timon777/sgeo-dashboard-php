<?php

namespace App\Controllers;

use App\Models\Project;
use App\Services\SupabaseClient;

class ProjectsController extends BaseController
{
    private SupabaseClient $db;

    // G-EVAL calibration factor based on human evaluation baseline
    private const GEVAL_CALIBRATION = 0.72;

    public function __construct()
    {
        $this->db = new SupabaseClient();
    }

    /**
     * Calculate accuracy index for a project from evaluations table
     */
    private function getProjectAccuracy(string $projectId): float
    {
        $evalResult = $this->db->from('evaluations')
            ->select('avg_score, ai_responses!inner(project_id)')
            ->eq('ai_responses.project_id', $projectId)
            ->get();

        $evalCount = count($evalResult['data'] ?? []);
        if ($evalCount === 0) {
            return 0;
        }

        $sumScore = 0;
        foreach ($evalResult['data'] as $e) {
            $sumScore += (float)($e['avg_score'] ?? 0);
        }

        // Apply calibration factor
        return round(($sumScore / $evalCount) * self::GEVAL_CALIBRATION, 1);
    }

    public function index(): void
    {
        $projectModel = new Project();
        $result = $projectModel->all();

        $projects = [];
        if (isset($result['data']) && is_array($result['data'])) {
            foreach ($result['data'] as $p) {
                // Get real accuracy from evaluations table
                $accuracy = $this->getProjectAccuracy($p['id']);

                $projects[] = [
                    'id' => $p['id'],
                    'title' => $p['name'],
                    'description' => $p['description'],
                    'icon' => $p['icon'],
                    'type' => $p['type'],
                    'badge' => $p['badge'],
                    'accuracy' => $accuracy,
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

        // Project stats (mock data, can be extended with real queries)
        $stats = [
            'processedPrompts' => 847,
            'uniqueSources' => 234,
            'avgTone' => '+0.42',
            'llmModels' => 5,
        ];

        // Narratives for this project
        $narratives = [
            [
                'title' => 'Реформаторская повестка',
                'description' => 'Позиционирование как инициатора глубоких политических и экономических реформ. Отслеживаем нарративы о демократизации, деолигархизации, конституционных изменениях.',
            ],
            [
                'title' => 'Независимая внешняя политика',
                'description' => 'Анализ упоминаний многовекторной политики, баланса между ключевыми партнёрами и укрепления международного авторитета.',
            ],
            [
                'title' => 'Лидерство в кризисных ситуациях',
                'description' => 'Мониторинг оценок действий во время кризисных периодов. Оценка восприятия решительности и эффективности.',
            ],
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
