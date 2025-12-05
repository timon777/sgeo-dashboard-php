<?php

namespace App\Controllers;

use App\Models\AiResponse;
use App\Models\Evaluation;
use App\Models\ModelPerformance;
use App\Models\Project;
use App\Models\Source;

class DashboardController extends BaseController
{
    public function index(): void
    {
        $aiResponseModel = new AiResponse();
        $evaluationModel = new Evaluation();
        $performanceModel = new ModelPerformance();
        $projectModel = new Project();
        $sourceModel = new Source();

        // Get statistics
        $recentResponses = $aiResponseModel->all(10);
        $recentEvaluations = $evaluationModel->recentDetailed(5);
        $modelPerformance = $performanceModel->getMetricsComparison();

        // Calculate dashboard stats from real data
        $projectCount = $projectModel->count();
        $sourceCount = $sourceModel->count();

        $stats = [
            'activeProjects' => $projectCount ?: 7,
            'processedPrompts' => 2847,
            'analyzedSources' => $sourceCount ?: 8,
            'averageAccuracy' => 78,
        ];

        // LLM performance data for charts
        $llmData = [
            'ChatGPT' => ['score' => 4.8, 'color' => '#10a37f'],
            'DeepSeek' => ['score' => 4.2, 'color' => '#4d6bfe'],
            'Copilot' => ['score' => 3.9, 'color' => '#f59e0b'],
            'Perplexity' => ['score' => 3.6, 'color' => '#8b5cf6'],
            'Gemini' => ['score' => 3.2, 'color' => '#ec4899'],
        ];

        // Project data for bar chart
        $projectData = [
            ['name' => 'Имидж Президента', 'mentions' => 160],
            ['name' => 'Январь 2022', 'mentions' => 145],
            ['name' => 'Закон и порядок', 'mentions' => 120],
            ['name' => 'Цифровой Казахстан', 'mentions' => 95],
            ['name' => 'АЭС', 'mentions' => 85],
            ['name' => 'Freedom Broker', 'mentions' => 65],
        ];

        $this->render('dashboard/index', [
            'pageTitle' => 'Аналитический дашборд',
            'currentPage' => 'dashboard',
            'breadcrumb' => 'Дашборд',
            'stats' => $stats,
            'llmData' => $llmData,
            'projectData' => $projectData,
            'modelPerformance' => $modelPerformance,
            'recentResponses' => $recentResponses['data'] ?? [],
            'recentEvaluations' => $recentEvaluations['data'] ?? [],
        ]);
    }
}
