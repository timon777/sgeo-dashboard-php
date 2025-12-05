<?php

namespace App\Controllers;

use App\Models\AiResponse;
use App\Models\Evaluation;
use App\Models\ModelPerformance;
use App\Models\Project;
use App\Models\Source;
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

        // Get real statistics from database
        $recentResponses = $aiResponseModel->all(10);
        $recentEvaluations = $evaluationModel->recentDetailed(5);
        $modelPerformance = $performanceModel->getMetricsComparison();

        // Calculate dashboard stats from real data
        $projectCount = $projectModel->count();
        $sourceCount = $sourceModel->count();
        $responseStats = $aiResponseModel->getStats();
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
        $result = $projectModel->all();
        $projectData = [];

        if (isset($result['data']) && is_array($result['data'])) {
            // Get response counts per project
            $db = new SupabaseClient();

            foreach ($result['data'] as $project) {
                $projectId = $project['id'];
                $projectName = $project['name'];

                // Truncate long names
                if (mb_strlen($projectName) > 20) {
                    $projectName = mb_substr($projectName, 0, 17) . '...';
                }

                // Count responses for this project
                $countResult = $db->from('ai_responses')
                    ->select('*')
                    ->eq('project_id', $projectId)
                    ->get();

                $mentions = count($countResult['data'] ?? []);

                $projectData[] = [
                    'name' => $projectName,
                    'mentions' => $mentions,
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
