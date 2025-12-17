<?php

namespace App\Controllers;

use App\Models\AiResponse;
use App\Models\Source;

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

        // Get filtered project count for sidebar
        $projectFilter = $this->getProjectFilter();
        $projectCount = Cache::remember("trends_project_count_{$projectFilter}", function() {
            $db = new \App\Services\SupabaseClient();
            $result = $db->from('projects')->select('*')->get();
            $count = 0;
            if (isset($result['data']) && is_array($result['data'])) {
                foreach ($result['data'] as $project) {
                    if ($this->canAccessProject($project)) {
                        $count++;
                    }
                }
            }
            return $count;
        }, 600);

        $this->render('trends/index', [
            'pageTitle' => 'Тренды и динамика',
            'currentPage' => 'trends',
            'breadcrumb' => 'Тренды',
            'stats' => $stats,
            'topProjects' => $topProjects,
            'chartData' => $chartData,
            'currentPeriod' => $period,
            'periodConfig' => $this->periodConfig,
            'projectCount' => $projectCount,
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

        // Base values that scale with period
        $baseAccuracy = 78;
        $baseRequests = 400;
        $baseProblematic = 18;
        $baseSources = 13;

        // Scale based on period
        $multiplier = match($period) {
            '24h' => 0.15,
            '7d' => 1,
            '30d' => 4.3,
            '90d' => 13,
            default => 1,
        };

        // Add some variation based on period
        $accuracyVariation = match($period) {
            '24h' => rand(-2, 3),
            '7d' => 0,
            '30d' => rand(-1, 2),
            '90d' => rand(-3, 1),
            default => 0,
        };

        return [
            'avgAccuracy' => $baseAccuracy + $accuracyVariation,
            'processedRequests' => (int)round($baseRequests * $multiplier),
            'problematicResponses' => (int)round($baseProblematic * $multiplier),
            'newSources' => (int)round($baseSources * $multiplier),
        ];
    }

    private function getTopProjectsForPeriod(string $period): array
    {
        // Different growth rates per period
        $projectsData = [
            '24h' => [
                ['name' => 'Цифровой Казахстан', 'growth' => '+2.5%', 'icon' => '💻', 'type' => 'gov'],
                ['name' => 'Имидж Президента', 'growth' => '+1.8%', 'icon' => '🏛️', 'type' => 'gov'],
                ['name' => 'Freedom Bank', 'growth' => '+1.5%', 'icon' => '🏦', 'type' => 'private'],
                ['name' => 'Закон и порядок', 'growth' => '+1.2%', 'icon' => '⚖️', 'type' => 'gov'],
                ['name' => 'АЭС', 'growth' => '+0.8%', 'icon' => '⚛️', 'type' => 'gov'],
            ],
            '7d' => [
                ['name' => 'Цифровой Казахстан', 'growth' => '+12%', 'icon' => '💻', 'type' => 'gov'],
                ['name' => 'Имидж Президента', 'growth' => '+10%', 'icon' => '🏛️', 'type' => 'gov'],
                ['name' => 'Закон и порядок', 'growth' => '+8%', 'icon' => '⚖️', 'type' => 'gov'],
                ['name' => 'Freedom Bank', 'growth' => '+7%', 'icon' => '🏦', 'type' => 'private'],
                ['name' => 'Январь 2022', 'growth' => '+5%', 'icon' => '📅', 'type' => 'gov'],
            ],
            '30d' => [
                ['name' => 'Имидж Президента', 'growth' => '+28%', 'icon' => '🏛️', 'type' => 'gov'],
                ['name' => 'Цифровой Казахстан', 'growth' => '+24%', 'icon' => '💻', 'type' => 'gov'],
                ['name' => 'АЭС', 'growth' => '+19%', 'icon' => '⚛️', 'type' => 'gov'],
                ['name' => 'Закон и порядок', 'growth' => '+15%', 'icon' => '⚖️', 'type' => 'gov'],
                ['name' => 'Freedom Bank', 'growth' => '+12%', 'icon' => '🏦', 'type' => 'private'],
            ],
            '90d' => [
                ['name' => 'АЭС', 'growth' => '+45%', 'icon' => '⚛️', 'type' => 'gov'],
                ['name' => 'Имидж Президента', 'growth' => '+38%', 'icon' => '🏛️', 'type' => 'gov'],
                ['name' => 'Цифровой Казахстан', 'growth' => '+35%', 'icon' => '💻', 'type' => 'gov'],
                ['name' => 'Январь 2022', 'growth' => '+22%', 'icon' => '📅', 'type' => 'gov'],
                ['name' => 'Freedom Bank', 'growth' => '+18%', 'icon' => '🏦', 'type' => 'private'],
            ],
        ];

        $allProjects = $projectsData[$period] ?? $projectsData['7d'];

        // Filter projects based on user access
        $filteredProjects = [];
        foreach ($allProjects as $project) {
            // Create a pseudo-project array for canAccessProject check
            $pseudoProject = [
                'name' => $project['name'],
                'type' => $project['type'] ?? 'gov',
            ];

            if ($this->canAccessProject($pseudoProject)) {
                $filteredProjects[] = $project;
            }
        }

        return $filteredProjects;
    }

    private function getChartDataForPeriod(string $period): array
    {
        $labels = $this->periodConfig[$period]['chartLabels'];

        // Generate chart data based on period
        $mainTrendData = match($period) {
            '24h' => [
                'labels' => $labels,
                'datasets' => [
                    ['label' => 'Имидж Президента', 'data' => [75, 76, 77, 78, 77, 78, 78]],
                    ['label' => 'Январь 2022', 'data' => [71, 71, 72, 72, 71, 72, 72]],
                    ['label' => 'Цифровой Казахстан', 'data' => [84, 84, 85, 85, 84, 85, 85]],
                    ['label' => 'АЭС', 'data' => [69, 69, 69, 70, 69, 69, 69]],
                ],
            ],
            '7d' => [
                'labels' => $labels,
                'datasets' => [
                    ['label' => 'Имидж Президента', 'data' => [68, 70, 72, 74, 75, 77, 78]],
                    ['label' => 'Январь 2022', 'data' => [65, 67, 68, 70, 71, 71, 72]],
                    ['label' => 'Цифровой Казахстан', 'data' => [80, 81, 82, 83, 84, 84, 85]],
                    ['label' => 'АЭС', 'data' => [72, 71, 70, 70, 69, 69, 69]],
                ],
            ],
            '30d' => [
                'labels' => $labels,
                'datasets' => [
                    ['label' => 'Имидж Президента', 'data' => [62, 70, 75, 78]],
                    ['label' => 'Январь 2022', 'data' => [58, 64, 68, 72]],
                    ['label' => 'Цифровой Казахстан', 'data' => [76, 80, 83, 85]],
                    ['label' => 'АЭС', 'data' => [74, 72, 70, 69]],
                ],
            ],
            '90d' => [
                'labels' => $labels,
                'datasets' => [
                    ['label' => 'Имидж Президента', 'data' => [55, 68, 78]],
                    ['label' => 'Январь 2022', 'data' => [50, 62, 72]],
                    ['label' => 'Цифровой Казахстан', 'data' => [70, 78, 85]],
                    ['label' => 'АЭС', 'data' => [45, 58, 69]],
                ],
            ],
            default => [
                'labels' => $labels,
                'datasets' => [],
            ],
        };

        $llmDistData = match($period) {
            '24h' => [180, 124, 105, 92, 60],
            '7d' => [1247, 856, 723, 634, 412],
            '30d' => [5340, 3680, 3102, 2720, 1768],
            '90d' => [16020, 11040, 9306, 8160, 5304],
            default => [1247, 856, 723, 634, 412],
        };

        return [
            'mainTrend' => $mainTrendData,
            'llmDistribution' => [
                'labels' => ['ChatGPT', 'DeepSeek', 'Gemini', 'Grok', 'Perplexity'],
                'data' => $llmDistData,
            ],
        ];
    }
}
