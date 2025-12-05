<?php

namespace App\Controllers;

use App\Models\Project;

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
