<?php

namespace App\Controllers;

use App\Models\Project;
use App\Services\Cache;
use App\Services\SupabaseClient;

class TrendsController extends BaseController
{
    private array $periodConfig = [
        '24h' => ['days' => 1, 'label' => '24ч', 'chartLabels' => ['00:00', '04:00', '08:00', '12:00', '16:00', '20:00', '24:00']],
        '7d' => ['days' => 7, 'label' => '7д', 'chartLabels' => ['Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс']],
        '30d' => ['days' => 30, 'label' => '30д', 'chartLabels' => ['Нед 1', 'Нед 2', 'Нед 3', 'Нед 4']],
        '90d' => ['days' => 90, 'label' => '90д', 'chartLabels' => ['Месяц 1', 'Месяц 2', 'Месяц 3']],
    ];

    public function index(): void
    {
        $period = $_GET['period'] ?? '7d';
        if (!isset($this->periodConfig[$period])) {
            $period = '7d';
        }

        $stats = $this->getStatsForPeriod($period);
        $topProjects = $this->getTopProjectsForPeriod($period);
        $chartData = $this->getChartDataForPeriod($period);

        $this->render('trends/index', [
            'pageTitle' => 'Тренды и динамика',
            'currentPage' => 'trends',
            'breadcrumb' => 'Тренды',
            'stats' => $stats,
            'topProjects' => $topProjects,
            'chartData' => $chartData,
            'currentPeriod' => $period,
            'periodConfig' => $this->periodConfig,
        ]);
    }

    public function apiChartData(): void
    {
        header('Content-Type: application/json');

        $period = $_GET['period'] ?? '7d';
        if (!isset($this->periodConfig[$period])) {
            $period = '7d';
        }

        $stats = $this->getStatsForPeriod($period);
        $topProjects = $this->getTopProjectsForPeriod($period);
        $chartData = $this->getChartDataForPeriod($period);

        echo json_encode([
            'success' => true,
            'stats' => $stats,
            'topProjects' => $topProjects,
            'chartData' => $chartData,
            'period' => $period,
        ]);
    }

    private function getStatsForPeriod(string $period): array
    {
        $days = $this->periodConfig[$period]['days'];

        return Cache::remember("trends_stats_{$period}", function() use ($days) {
            $db = new SupabaseClient();
            $since = date('Y-m-d\TH:i:s', strtotime("-{$days} days"));

            $responsesResult = $db->from('ai_responses')
                ->select('id, risk_level')
                ->gte('created_at', $since)
                ->get();
            $responses = $responsesResult['data'] ?? [];

            $evaluationsResult = $db->from('evaluations')
                ->select('avg_score')
                ->gte('evaluated_at', $since)
                ->get();
            $evaluations = $evaluationsResult['data'] ?? [];

            $avgAccuracy = 0;
            if (!empty($evaluations)) {
                $totalScore = 0;
                foreach ($evaluations as $e) {
                    $totalScore += (float)($e['avg_score'] ?? 0);
                }
                $avgAccuracy = round($totalScore / count($evaluations));
            }

            $sourcesResult = $db->from('sources')
                ->select('id')
                ->gte('created_at', $since)
                ->get();

            $problematicCount = 0;
            foreach ($responses as $r) {
                if (($r['risk_level'] ?? 'low') === 'high') {
                    $problematicCount++;
                }
            }

            return [
                'avgAccuracy' => $avgAccuracy,
                'processedRequests' => count($responses),
                'problematicResponses' => $problematicCount,
                'newSources' => count($sourcesResult['data'] ?? []),
            ];
        }, 300);
    }

    private function getTopProjectsForPeriod(string $period): array
    {
        $days = $this->periodConfig[$period]['days'];

        return Cache::remember("trends_top_projects_{$period}", function() use ($days) {
            $db = new SupabaseClient();
            $since = date('Y-m-d\TH:i:s', strtotime("-{$days} days"));

            $projectsResult = $db->from('projects')
                ->select('id, name, icon, accuracy_score, trend_direction, trend_percent')
                ->eq('is_active', 'true')
                ->get();

            $responsesResult = $db->from('ai_responses')
                ->select('project_id')
                ->gte('created_at', $since)
                ->get();

            $responsesByProject = [];
            foreach ($responsesResult['data'] ?? [] as $r) {
                $pid = $r['project_id'] ?? null;
                if ($pid) {
                    $responsesByProject[$pid] = ($responsesByProject[$pid] ?? 0) + 1;
                }
            }

            $projects = [];
            foreach ($projectsResult['data'] ?? [] as $p) {
                $count = $responsesByProject[$p['id']] ?? 0;
                $trendSign = ($p['trend_direction'] ?? 'up') === 'up' ? '+' : '-';
                $trendPercent = (float)($p['trend_percent'] ?? 0);

                $projects[] = [
                    'name' => $p['name'],
                    'growth' => $trendSign . $trendPercent . '%',
                    'icon' => $p['icon'] ?? '📊',
                    'responses' => $count,
                ];
            }

            usort($projects, fn($a, $b) => $b['responses'] - $a['responses']);

            return array_slice($projects, 0, 5);
        }, 300);
    }

    private function getChartDataForPeriod(string $period): array
    {
        $days = $this->periodConfig[$period]['days'];
        $labels = $this->periodConfig[$period]['chartLabels'];

        return Cache::remember("trends_chart_{$period}", function() use ($days, $labels) {
            $db = new SupabaseClient();
            $since = date('Y-m-d\TH:i:s', strtotime("-{$days} days"));

            $projectsResult = $db->from('projects')
                ->select('id, name')
                ->eq('is_active', 'true')
                ->get();
            $projects = $projectsResult['data'] ?? [];

            $evaluationsResult = $db->from('evaluations')
                ->select('avg_score, evaluated_at, ai_responses!inner(project_id)')
                ->gte('evaluated_at', $since)
                ->order('evaluated_at', true)
                ->get();

            $bucketCount = count($labels);
            $projectScores = [];
            $projectNames = [];
            foreach ($projects as $p) {
                $projectScores[$p['id']] = array_fill(0, $bucketCount, ['sum' => 0, 'count' => 0]);
                $projectNames[$p['id']] = $p['name'];
            }

            $startTime = strtotime("-{$days} days");
            $endTime = time();
            $bucketSize = ($endTime - $startTime) / $bucketCount;

            foreach ($evaluationsResult['data'] ?? [] as $eval) {
                $projectId = $eval['ai_responses']['project_id'] ?? null;
                if (!$projectId || !isset($projectScores[$projectId])) continue;

                $evalTime = strtotime($eval['evaluated_at']);
                $bucket = (int)floor(($evalTime - $startTime) / $bucketSize);
                $bucket = max(0, min($bucketCount - 1, $bucket));

                $projectScores[$projectId][$bucket]['sum'] += (float)($eval['avg_score'] ?? 0);
                $projectScores[$projectId][$bucket]['count']++;
            }

            $colors = ['#8b5cf6', '#6366f1', '#22c55e', '#f59e0b', '#ec4899', '#ef4444', '#06b6d4'];
            $datasets = [];
            $colorIndex = 0;

            foreach ($projectScores as $projectId => $buckets) {
                $hasData = false;
                foreach ($buckets as $b) {
                    if ($b['count'] > 0) { $hasData = true; break; }
                }
                if (!$hasData) continue;

                $data = [];
                foreach ($buckets as $b) {
                    $data[] = $b['count'] > 0 ? round($b['sum'] / $b['count'], 1) : null;
                }

                $datasets[] = [
                    'label' => $projectNames[$projectId],
                    'data' => $data,
                ];
                $colorIndex++;
                if (count($datasets) >= 7) break;
            }

            $llmResult = $db->from('ai_responses')
                ->select('model_name')
                ->gte('created_at', $since)
                ->get();

            $llmCounts = [];
            foreach ($llmResult['data'] ?? [] as $r) {
                $model = $r['model_name'] ?? 'Unknown';
                $llmCounts[$model] = ($llmCounts[$model] ?? 0) + 1;
            }
            arsort($llmCounts);

            return [
                'mainTrend' => [
                    'labels' => $labels,
                    'datasets' => $datasets,
                ],
                'llmDistribution' => [
                    'labels' => !empty($llmCounts) ? array_keys($llmCounts) : [],
                    'data' => !empty($llmCounts) ? array_values($llmCounts) : [],
                ],
            ];
        }, 300);
    }
}
