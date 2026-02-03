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
        // Clear cache if requested via URL parameter
        if (isset($_GET['clear_cache']) && $_GET['clear_cache'] === '1') {
            Cache::flush();
        }

        $aiResponseModel = new AiResponse();
        $evaluationModel = new Evaluation();
        $performanceModel = new ModelPerformance();
        $projectModel = new Project();
        $sourceModel = new Source();

        // Cache expensive queries for 10 minutes
        $modelPerformance = Cache::remember('dashboard_model_performance', function() use ($performanceModel) {
            return $performanceModel->getMetricsComparison();
        }, 600);

        $recentResponses = Cache::remember('dashboard_recent_responses', function() use ($aiResponseModel) {
            return $aiResponseModel->all(10);
        }, 300);

        $recentEvaluations = Cache::remember('dashboard_recent_evaluations', function() use ($evaluationModel) {
            return $evaluationModel->recentDetailed(5);
        }, 300);

        // Calculate dashboard stats from real data with caching
        $projectCount = Cache::remember('dashboard_project_count', function() use ($projectModel) {
            return $projectModel->count();
        }, 600);

        $sourceCount = Cache::remember('dashboard_source_count', function() use ($sourceModel) {
            return $sourceModel->count();
        }, 600);

        $responseStats = Cache::remember('dashboard_response_stats', function() use ($aiResponseModel) {
            return $aiResponseModel->getStats();
        }, 600);

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

        // Get trend data (cached for 30 minutes - expensive operation)
        $trends = Cache::remember('dashboard_trends', function() {
            return $this->calculateTrends();
        }, 1800);

        // Get radar chart data from evaluations
        $radarData = $this->buildRadarChartData($modelPerformance);

        // Get source statistics for donut charts (cached for 5 minutes)
        $sourceStats = Cache::remember('dashboard_source_stats', function() use ($sourceModel) {
            return $this->buildSourceStats($sourceModel);
        }, 600);

        // Get project accuracy dynamics data for line chart
        $accuracyDynamics = Cache::remember('dashboard_accuracy_dynamics', function() {
            return $this->buildAccuracyDynamicsData();
        }, 600);

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
            'radarData' => $radarData,
            'sourceStats' => $sourceStats,
            'accuracyDynamics' => $accuracyDynamics,
        ]);
    }

    private function buildLlmChartData(array $modelPerformance): array
    {
        // Use lowercase keys for case-insensitive matching
        $colorMap = [
            'chatgpt' => '#10a37f',
            'gpt-4' => '#10a37f',
            'deepseek' => '#4d6bfe',
            'grok' => '#1d9bf0',
            'gemini' => '#4285f4',
            'perplexity' => '#8b5cf6',
            'claude' => '#d97706',
            'copilot' => '#f59e0b',
        ];

        // Display names mapping
        $displayNames = [
            'chatgpt' => 'ChatGPT',
            'gpt-4' => 'GPT-4',
            'deepseek' => 'DeepSeek',
            'grok' => 'Grok',
            'gemini' => 'Gemini',
            'perplexity' => 'Perplexity',
            'claude' => 'Claude',
            'copilot' => 'Copilot',
        ];

        $llmData = [];
        foreach ($modelPerformance as $modelName => $data) {
            // Convert 0-100 score to 0-5 scale for chart
            $score = round(($data['overall'] ?? 0) / 20, 1);
            $lowerName = strtolower($modelName);
            $displayName = $displayNames[$lowerName] ?? ucfirst($modelName);
            $llmData[$displayName] = [
                'score' => $score,
                'color' => $colorMap[$lowerName] ?? '#6366f1',
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
                // Count ai_responses per project directly
                $db = new SupabaseClient();
                $responsesResult = $db->from('ai_responses')->select('project_id')->get();

                $responsesByProject = [];
                if (isset($responsesResult['data'])) {
                    foreach ($responsesResult['data'] as $response) {
                        $pid = $response['project_id'] ?? null;
                        if ($pid) {
                            $responsesByProject[$pid] = ($responsesByProject[$pid] ?? 0) + 1;
                        }
                    }
                }

                foreach ($result['data'] as $project) {
                    $projectId = $project['id'];
                    $projectName = $project['name'];

                    // Truncate long names
                    if (mb_strlen($projectName) > 20) {
                        $projectName = mb_substr($projectName, 0, 17) . '...';
                    }

                    // Get mentions from ai_responses count
                    $mentions = $responsesByProject[$projectId] ?? 0;

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
        }, 600);
    }

    private function calculateTrends(): array
    {
        $db = new SupabaseClient();

        // Get stats from 7 days ago and today
        $today = date('Y-m-d');
        $weekAgo = date('Y-m-d', strtotime('-7 days'));

        // Get today's stats (or save if not exists)
        $todayStats = $this->getDailyStats($db, $today);
        $weekAgoStats = $this->getDailyStats($db, $weekAgo);

        // Calculate percentage changes
        $trends = [
            'projects' => $this->calcTrendPercent($weekAgoStats['total_projects'], $todayStats['total_projects']),
            'prompts' => $this->calcTrendPercent($weekAgoStats['total_prompts'], $todayStats['total_prompts']),
            'sources' => $this->calcTrendPercent($weekAgoStats['total_sources'], $todayStats['total_sources']),
            'accuracy' => $this->calcTrendPercent($weekAgoStats['avg_accuracy'], $todayStats['avg_accuracy']),
        ];

        return $trends;
    }

    private function getDailyStats(SupabaseClient $db, string $date): array
    {
        $result = $db->from('daily_stats')
            ->select('*')
            ->eq('stat_date', $date)
            ->single();

        if ($result && isset($result['id'])) {
            return $result;
        }

        // If today and no stats, calculate and save
        if ($date === date('Y-m-d')) {
            return $this->saveDailySnapshot($db);
        }

        // Return zeros for missing historical data
        return [
            'total_projects' => 0,
            'total_prompts' => 0,
            'total_sources' => 0,
            'avg_accuracy' => 0,
            'total_responses' => 0,
        ];
    }

    private function saveDailySnapshot(SupabaseClient $db): array
    {
        $projectModel = new Project();
        $sourceModel = new Source();
        $aiResponseModel = new AiResponse();
        $performanceModel = new ModelPerformance();

        $projectCount = $projectModel->count();
        $sourceCount = $sourceModel->count();
        $responseStats = $aiResponseModel->getStats();
        $promptCount = $responseStats['total'] ?? 0;

        // Calculate average accuracy
        $modelPerformance = $performanceModel->getMetricsComparison();
        $avgAccuracy = 0;
        if (!empty($modelPerformance)) {
            $totalScore = 0;
            foreach ($modelPerformance as $data) {
                $totalScore += $data['overall'] ?? 0;
            }
            $avgAccuracy = round($totalScore / count($modelPerformance), 2);
        }

        $stats = [
            'stat_date' => date('Y-m-d'),
            'total_projects' => $projectCount,
            'total_prompts' => $promptCount,
            'total_sources' => $sourceCount,
            'avg_accuracy' => $avgAccuracy,
            'total_responses' => $promptCount,
        ];

        // Upsert - try insert, if conflict update
        $existing = $db->from('daily_stats')
            ->select('id')
            ->eq('stat_date', date('Y-m-d'))
            ->single();

        if ($existing && isset($existing['id'])) {
            $db->from('daily_stats')
                ->eq('id', $existing['id'])
                ->update($stats);
        } else {
            $db->from('daily_stats')->insert($stats);
        }

        return $stats;
    }

    private function calcTrendPercent($old, $new): array
    {
        $old = (float)$old;
        $new = (float)$new;

        if ($old == 0 && $new == 0) {
            return ['value' => '0%', 'up' => true];
        }

        if ($old == 0) {
            return ['value' => '+100%', 'up' => true];
        }

        $percent = round((($new - $old) / $old) * 100);
        $isUp = $percent >= 0;
        $value = ($isUp ? '+' : '') . $percent . '%';

        return ['value' => $value, 'up' => $isUp];
    }

    private function buildRadarChartData(array $modelPerformance): array
    {
        $metrics = [
            'coherence' => 0,
            'consistency' => 0,
            'fluency' => 0,
            'relevance' => 0,
        ];

        $count = count($modelPerformance);
        if ($count > 0) {
            foreach ($modelPerformance as $data) {
                $metrics['coherence'] += $data['coherence'] ?? 0;
                $metrics['consistency'] += $data['consistency'] ?? 0;
                $metrics['fluency'] += $data['fluency'] ?? 0;
                $metrics['relevance'] += $data['relevance'] ?? 0;
            }
            foreach ($metrics as $key => $value) {
                $metrics[$key] = round($value / $count);
            }
        }

        $avgScore = $count > 0 ? round(array_sum($metrics) / count($metrics), 1) : 0;

        // E-E-A-T from actual source data
        $eeatMetrics = $this->getSourceEeatAverages();
        $eeatAvg = !empty(array_filter($eeatMetrics)) ? round(array_sum($eeatMetrics) / count($eeatMetrics), 1) : 0;

        return [
            'prompts' => [
                'labels' => ['Нейтральность', 'Стабильность', 'Корректность', 'Анти-гал.'],
                'data' => [
                    $metrics['coherence'],
                    $metrics['consistency'],
                    $metrics['fluency'],
                    $metrics['relevance']
                ],
                'avg' => $avgScore,
            ],
            'answers' => [
                'labels' => ['Связность', 'Согласованность', 'Беглость', 'Релевантность'],
                'data' => [
                    $metrics['coherence'],
                    $metrics['consistency'],
                    $metrics['fluency'],
                    $metrics['relevance']
                ],
                'avg' => $avgScore,
            ],
            'eeat' => [
                'labels' => ['Опыт', 'Экспертность', 'Авторитетность', 'Доверие'],
                'data' => [
                    $eeatMetrics['experience'],
                    $eeatMetrics['expertise'],
                    $eeatMetrics['authority'],
                    $eeatMetrics['trust']
                ],
                'avg' => $eeatAvg,
            ],
        ];
    }

    private function getSourceEeatAverages(): array
    {
        return Cache::remember('dashboard_source_eeat', function() {
            $db = new SupabaseClient();
            $result = $db->from('sources')
                ->select('experience_score, expertise_score, authority_score, trust_score')
                ->get();

            $totals = ['experience' => 0, 'expertise' => 0, 'authority' => 0, 'trust' => 0];
            $count = 0;

            foreach ($result['data'] ?? [] as $source) {
                $totals['experience'] += (int)($source['experience_score'] ?? 0);
                $totals['expertise'] += (int)($source['expertise_score'] ?? 0);
                $totals['authority'] += (int)($source['authority_score'] ?? 0);
                $totals['trust'] += (int)($source['trust_score'] ?? 0);
                $count++;
            }

            if ($count === 0) {
                return $totals;
            }

            return [
                'experience' => round($totals['experience'] / $count),
                'expertise' => round($totals['expertise'] / $count),
                'authority' => round($totals['authority'] / $count),
                'trust' => round($totals['trust'] / $count),
            ];
        }, 600);
    }

    private function buildSourceStats(Source $sourceModel): array
    {
        $db = new SupabaseClient();

        // Get all sources with one query instead of multiple
        $sourcesResult = $db->from('sources')
            ->select('country, type')
            ->get();

        $geoCounts = ['kz' => 0, 'ru' => 0, 'us' => 0, 'other' => 0];
        $typeCounts = ['media' => 0, 'gov' => 0, 'social' => 0, 'blog' => 0, 'science' => 0];

        if (isset($sourcesResult['data'])) {
            foreach ($sourcesResult['data'] as $source) {
                // Count by country
                $country = strtolower($source['country'] ?? 'other');
                if (isset($geoCounts[$country])) {
                    $geoCounts[$country]++;
                } else {
                    $geoCounts['other']++;
                }

                // Count by type
                $type = strtolower($source['type'] ?? 'other');
                if ($type === 'media' || $type === 'сми') {
                    $typeCounts['media']++;
                } elseif ($type === 'gov' || $type === 'government' || $type === 'государственный') {
                    $typeCounts['gov']++;
                } elseif ($type === 'social' || $type === 'социальные сети') {
                    $typeCounts['social']++;
                } elseif ($type === 'blog' || $type === 'блог') {
                    $typeCounts['blog']++;
                } elseif ($type === 'science' || $type === 'научный') {
                    $typeCounts['science']++;
                }
            }
        }

        // Get LLM distribution from ai_responses
        $llmResult = $db->from('ai_responses')
            ->select('model_name')
            ->get();

        $llmCounts = [];
        if (isset($llmResult['data'])) {
            foreach ($llmResult['data'] as $response) {
                $model = $response['model_name'] ?? 'Unknown';
                $llmCounts[$model] = ($llmCounts[$model] ?? 0) + 1;
            }
        }

        // Convert to percentages
        $totalGeo = array_sum($geoCounts) ?: 1;
        $totalType = array_sum($typeCounts) ?: 1;
        $totalLlm = array_sum($llmCounts) ?: 1;

        return [
            'geography' => [
                'labels' => ['Казахстанские', 'Российские', 'Американские', 'Прочие'],
                'data' => [
                    round($geoCounts['kz'] / $totalGeo * 100),
                    round($geoCounts['ru'] / $totalGeo * 100),
                    round($geoCounts['us'] / $totalGeo * 100),
                    round($geoCounts['other'] / $totalGeo * 100),
                ],
            ],
            'types' => [
                'labels' => ['СМИ', 'Государственные', 'Соцсети', 'Блоги', 'Научные'],
                'data' => [
                    round($typeCounts['media'] / $totalType * 100),
                    round($typeCounts['gov'] / $totalType * 100),
                    round($typeCounts['social'] / $totalType * 100),
                    round($typeCounts['blog'] / $totalType * 100),
                    round($typeCounts['science'] / $totalType * 100),
                ],
            ],
            'llm' => [
                'labels' => array_keys($llmCounts),
                'data' => array_map(function($count) use ($totalLlm) {
                    return round($count / $totalLlm * 100);
                }, array_values($llmCounts)),
            ],
        ];
    }

    private function buildAccuracyDynamicsData(): array
    {
        $db = new SupabaseClient();

        // Get projects
        $projectsResult = $db->from('projects')->select('id, name')->get();
        $projects = $projectsResult['data'] ?? [];

        // Get evaluations with project info for last 7 days
        $weekAgo = date('Y-m-d', strtotime('-7 days'));
        $evaluationsResult = $db->from('evaluations')
            ->select('avg_score, evaluated_at, ai_responses!inner(project_id)')
            ->gte('evaluated_at', $weekAgo)
            ->order('evaluated_at', true)
            ->get();

        // Group evaluations by project and day
        $projectScores = [];
        $projectNames = [];
        foreach ($projects as $p) {
            $projectScores[$p['id']] = [];
            $projectNames[$p['id']] = $p['name'];
        }

        // Organize scores by day of week
        $dayLabels = ['Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс'];

        foreach ($evaluationsResult['data'] ?? [] as $eval) {
            $projectId = $eval['ai_responses']['project_id'] ?? null;
            if (!$projectId || !isset($projectScores[$projectId])) continue;

            $dayOfWeek = (int)date('N', strtotime($eval['evaluated_at'])) - 1; // 0-6
            if (!isset($projectScores[$projectId][$dayOfWeek])) {
                $projectScores[$projectId][$dayOfWeek] = ['sum' => 0, 'count' => 0];
            }
            $projectScores[$projectId][$dayOfWeek]['sum'] += (float)$eval['avg_score'];
            $projectScores[$projectId][$dayOfWeek]['count']++;
        }

        // Build datasets
        $colors = ['#8b5cf6', '#6366f1', '#22c55e', '#f59e0b', '#ec4899', '#ef4444', '#06b6d4', '#10b981'];
        $datasets = [];
        $colorIndex = 0;

        foreach ($projectScores as $projectId => $dayData) {
            // Skip projects with no data
            if (empty(array_filter($dayData))) continue;

            $data = [];
            for ($i = 0; $i < 7; $i++) {
                if (isset($dayData[$i]) && $dayData[$i]['count'] > 0) {
                    $data[] = round($dayData[$i]['sum'] / $dayData[$i]['count'], 1);
                } else {
                    // Use project's last known score or estimate
                    $data[] = null;
                }
            }

            // Fill nulls with interpolated values or average
            $validValues = array_filter($data, fn($v) => $v !== null);
            $avg = !empty($validValues) ? round(array_sum($validValues) / count($validValues), 1) : null;
            $data = array_map(fn($v) => $v ?? $avg, $data);

            $datasets[] = [
                'label' => $projectNames[$projectId],
                'data' => $data,
                'color' => $colors[$colorIndex % count($colors)],
            ];
            $colorIndex++;

            // Limit to 7 projects
            if (count($datasets) >= 7) break;
        }


        return [
            'labels' => $dayLabels,
            'datasets' => $datasets,
        ];
    }
}
