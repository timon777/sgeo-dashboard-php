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

        // Get statistics from database
        $recentResponses = $aiResponseModel->all(10);
        $recentEvaluations = $evaluationModel->recentDetailed(5);
        $modelPerformance = $performanceModel->getMetricsComparison();

        // Get real counts
        $projectResult = $projectModel->all();
        $projectCount = count($projectResult['data'] ?? []);
        $sourceCount = $sourceModel->count();

        // Get prompts count from ai_responses
        $responseStats = $aiResponseModel->getStats();
        $promptsCount = $responseStats['total'] ?? 0;

        // Calculate average accuracy from model performance
        $avgAccuracy = 0;
        if (!empty($modelPerformance)) {
            $totalScore = 0;
            foreach ($modelPerformance as $model) {
                $totalScore += $model['overall'] ?? 0;
            }
            $avgAccuracy = count($modelPerformance) > 0 ? round($totalScore / count($modelPerformance)) : 0;
        }

        $stats = [
            'activeProjects' => $projectCount,
            'processedPrompts' => $promptsCount,
            'analyzedSources' => $sourceCount,
            'averageAccuracy' => $avgAccuracy,
        ];

        // LLM performance data from database
        $llmData = [];
        foreach ($modelPerformance as $modelName => $metrics) {
            $colors = [
                'gpt-4' => '#10a37f',
                'gpt-3.5-turbo' => '#10a37f',
                'chatgpt' => '#10a37f',
                'deepseek' => '#4d6bfe',
                'gemini' => '#4285f4',
                'gemini-pro' => '#4285f4',
                'grok' => '#1d9bf0',
                'perplexity' => '#8b5cf6',
                'claude' => '#6366f1',
            ];

            $color = '#6366f1'; // default
            foreach ($colors as $key => $c) {
                if (stripos($modelName, $key) !== false) {
                    $color = $c;
                    break;
                }
            }

            $llmData[$modelName] = [
                'score' => round($metrics['overall'] / 20, 1), // Convert 0-100 to 0-5 scale
                'color' => $color,
            ];
        }

        // Project data from database
        $projectData = [];
        if (!empty($projectResult['data'])) {
            foreach (array_slice($projectResult['data'], 0, 6) as $p) {
                $projectData[] = [
                    'name' => $p['name'] ?? 'Unknown',
                    'mentions' => $p['mentions_count'] ?? 0,
                ];
            }
        }

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
