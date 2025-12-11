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
                // Calculate real accuracy from evaluations if not set
                $accuracy = (float)($p['accuracy_score'] ?? 0);
                if ($accuracy === 0.0) {
                    $accuracy = $projectModel->calculateAccuracyIndex($p['id']);
                }

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

        // Get real project statistics from database
        $stats = $projectModel->getProjectStats($id);

        // Get real metrics for charts
        $metrics = $projectModel->getProjectMetrics($id);

        // Get source distribution for charts
        $sourceDistribution = $projectModel->getSourceDistribution($id);

        // Get LLM distribution for charts
        $llmDistribution = $projectModel->getLlmDistribution($id);

        // Get narratives from database
        $narratives = $projectModel->getNarratives($id);

        // Calculate real accuracy index if not set in project
        $accuracyIndex = (float)($project['accuracy_score'] ?? 0);
        if ($accuracyIndex === 0.0) {
            $accuracyIndex = $projectModel->calculateAccuracyIndex($id);
            $project['accuracy_score'] = $accuracyIndex;
        }

        $this->render('projects/show', [
            'pageTitle' => $project['name'],
            'currentPage' => 'projects',
            'project' => $project,
            'stats' => $stats,
            'metrics' => $metrics,
            'sourceDistribution' => $sourceDistribution,
            'llmDistribution' => $llmDistribution,
            'narratives' => $narratives,
        ]);
    }
}
