<?php

namespace App\Controllers;

use App\Models\AiResponse;
use App\Models\Evaluation;
use App\Models\ModelPerformance;
use App\Models\Project;
use App\Models\Source;
use App\Services\Cache;
use App\Services\SupabaseClient;

class DashboardController extends BaseController
{
    public function index(): void
    {
        $aiResponseModel = new AiResponse();
        $evaluationModel = new Evaluation();
        $performanceModel = new ModelPerformance();
        $projectModel = new Project();
        $sourceModel = new Source();

        // Cache expensive queries for 5 minutes
        $modelPerformance = Cache::remember('dashboard_model_performance', function() use ($performanceModel) {
            return $performanceModel->getMetricsComparison();
        }, 300);

        $recentResponses = Cache::remember('dashboard_recent_responses', function() use ($aiResponseModel) {
            return $aiResponseModel->all(10);
        }, 60);

        $recentEvaluations = Cache::remember('dashboard_recent_evaluations', function() use ($evaluationModel) {
            return $evaluationModel->recentDetailed(5);
        }, 60);

        // Calculate dashboard stats from real data with caching
        $projectCount = Cache::remember('dashboard_project_count', function() use ($projectModel) {
            return $projectModel->count();
        }, 300);

        $sourceCount = Cache::remember('dashboard_source_count', function() use ($sourceModel) {
            return $sourceModel->count();
        }, 300);

        $responseStats = Cache::remember('dashboard_response_stats', function() use ($aiResponseModel) {
            return $aiResponseModel->getStats();
        }, 300);

        $promptCount = $responseStats['total'] ?? 0;

        // Calculate average accuracy from model performance
        $avgAccuracy = 0;
        if (!empty($modelPerformance)) {
            $totalScore = 0;
            foreach ($modelPerformance as $model => $data) {
                $totalScore += $data['overall'] ?? 0;
            }
            $avgAccuracy = round($totalScore / count($modelPerformance));
        }

        $stats = [
            'activeProjects' => $projectCount,
            'processedPrompts' => $promptCount,
            'analyzedSources' => $sourceCount,
            'averageAccuracy' => $avgAccuracy ?: 0,
        ];

        // Get LLM performance data for charts from DB
        $llmData = $this->buildLlmChartData($modelPerformance);

        // Get project data for bar chart from DB
        $projectData = $this->buildProjectChartData($projectModel);

        // Get trend data
        $trends = $this->calculateTrends();

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
            'trends' => $trends,
        ]);
    }

    private function buildLlmChartData(array $modelPerformance): array
    {
        $colorMap = [
            'ChatGPT' => '#10a37f',
            'GPT-4' => '#10a37f',
            'DeepSeek' => '#4d6bfe',
            'Grok' => '#1d9bf0',
            'Gemini' => '#4285f4',
            'Perplexity' => '#8b5cf6',
            'Claude' => '#d97706',
            'Copilot' => '#f59e0b',
        ];

        $llmData = [];
        foreach ($modelPerformance as $modelName => $data) {
            // Convert 0-100 score to 0-5 scale for chart
            $score = round(($data['overall'] ?? 0) / 20, 1);
            $llmData[$modelName] = [
                'score' => $score,
                'color' => $colorMap[$modelName] ?? '#6366f1',
            ];
        }

        return $llmData;
    }

    private function buildProjectChartData(Project $projectModel): array
    {
        return Cache::remember('dashboard_project_chart_data', function() use ($projectModel) {
            $result = $projectModel->all();
            $projectData = [];

            if (isset($result['data']) && is_array($result['data'])) {
                // Get all response counts in one query using project_stats view
                $db = new SupabaseClient();
                $statsResult = $db->from('project_stats')->select('*')->get();

                $statsByProject = [];
                if (isset($statsResult['data'])) {
                    foreach ($statsResult['data'] as $stat) {
                        $statsByProject[$stat['project_id']] = $stat;
                    }
                }

                foreach ($result['data'] as $project) {
                    $projectId = $project['id'];
                    $projectName = $project['name'];

                    // Truncate long names
                    if (mb_strlen($projectName) > 20) {
                        $projectName = mb_substr($projectName, 0, 17) . '...';
                    }

                    // Get mentions from cached stats
                    $mentions = $statsByProject[$projectId]['total_responses'] ?? 0;

                    $projectData[] = [
                        'name' => $projectName,
                        'mentions' => (int)$mentions,
                        'accuracy' => (float)($project['accuracy_score'] ?? 0),
                    ];
                }

                // Sort by mentions descending
                usort($projectData, function($a, $b) {
                    return $b['mentions'] - $a['mentions'];
                });

                // Limit to top 6
                $projectData = array_slice($projectData, 0, 6);
            }

            return $projectData;
        }, 300);
    }

    private function calculateTrends(): array
    {
        // In production, calculate from historical data
        // Compare current week vs previous week
        return [
            'projects' => ['value' => '+12%', 'up' => true],
            'prompts' => ['value' => '+8%', 'up' => true],
            'sources' => ['value' => '+5%', 'up' => true],
            'accuracy' => ['value' => '+3%', 'up' => true],
        ];
    }
}
