<?php

namespace App\Controllers;

use App\Models\Project;
use App\Models\AiResponse;
use App\Models\Source;
use App\Models\ModelPerformance;
use App\Services\SupabaseClient;

class TrendsController extends BaseController
{
    public function index(): void
    {
        $projectModel = new Project();
        $aiResponseModel = new AiResponse();
        $sourceModel = new Source();
        $performanceModel = new ModelPerformance();
        $db = new SupabaseClient();

        // Get real stats
        $responseStats = $aiResponseModel->getStats();
        $totalRequests = $responseStats['total'] ?? 0;

        // Get model performance for average accuracy
        $modelPerformance = $performanceModel->getMetricsComparison();
        $avgAccuracy = 0;
        if (!empty($modelPerformance)) {
            $totalScore = 0;
            foreach ($modelPerformance as $model) {
                $totalScore += $model['overall'] ?? 0;
            }
            $avgAccuracy = count($modelPerformance) > 0 ? round($totalScore / count($modelPerformance)) : 0;
        }

        // Get problematic responses count (low quality scores)
        $problematicResult = $db->from('ai_responses')
            ->select('count')
            ->lt('quality_score', 50)
            ->get();
        $problematicCount = $problematicResult['data'][0]['count'] ?? 0;

        // Get new sources count (last 30 days)
        $thirtyDaysAgo = date('Y-m-d', strtotime('-30 days'));
        $newSourcesResult = $db->from('sources')
            ->select('count')
            ->gte('created_at', $thirtyDaysAgo)
            ->get();
        $newSourcesCount = $newSourcesResult['data'][0]['count'] ?? 0;

        $stats = [
            'avgAccuracy' => $avgAccuracy,
            'processedRequests' => $totalRequests,
            'problematicResponses' => $problematicCount,
            'newSources' => $newSourcesCount,
        ];

        // Get top projects by growth/activity
        $projectsResult = $projectModel->all();
        $topProjects = [];

        if (isset($projectsResult['data']) && is_array($projectsResult['data'])) {
            // Sort by trend_percent descending
            $projects = $projectsResult['data'];
            usort($projects, function ($a, $b) {
                return ($b['trend_percent'] ?? 0) <=> ($a['trend_percent'] ?? 0);
            });

            foreach (array_slice($projects, 0, 5) as $p) {
                $trendPercent = $p['trend_percent'] ?? 0;
                $growth = ($trendPercent >= 0 ? '+' : '') . $trendPercent . '%';

                $topProjects[] = [
                    'name' => $p['name'] ?? 'Unknown',
                    'growth' => $growth,
                    'icon' => $p['icon'] ?? '📊',
                ];
            }
        }

        $this->render('trends/index', [
            'pageTitle' => 'Тренды и динамика',
            'currentPage' => 'trends',
            'breadcrumb' => 'Тренды',
            'stats' => $stats,
            'topProjects' => $topProjects,
        ]);
    }
}
