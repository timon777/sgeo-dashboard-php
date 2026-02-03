<?php

namespace App\Controllers;

use App\Models\ModelPerformance;
use App\Models\AiResponse;
use App\Models\Evaluation;
use App\Services\Cache;
use App\Services\SupabaseClient;

class LlmMonitoringController extends BaseController
{
    private array $modelConfig = [
        'ChatGPT' => ['shortName' => 'gpt', 'icon' => '🤖', 'color' => '#10a37f'],
        'GPT-4' => ['shortName' => 'gpt', 'icon' => '🤖', 'color' => '#10a37f'],
        'DeepSeek' => ['shortName' => 'deepseek', 'icon' => '🔍', 'color' => '#4d6bfe'],
        'Grok' => ['shortName' => 'grok', 'icon' => '⚡', 'color' => '#1d9bf0'],
        'Gemini' => ['shortName' => 'gemini', 'icon' => '💎', 'color' => '#4285f4'],
        'Perplexity' => ['shortName' => 'perplexity', 'icon' => '🌐', 'color' => '#8b5cf6'],
        'Claude' => ['shortName' => 'claude', 'icon' => '🎭', 'color' => '#d97706'],
        'Copilot' => ['shortName' => 'copilot', 'icon' => '✈️', 'color' => '#f59e0b'],
    ];

    public function index(): void
    {
        $performanceModel = new ModelPerformance();
        $aiResponseModel = new AiResponse();

        // Get model performance from DB with caching
        $modelPerformance = Cache::remember('llm_model_performance', function() use ($performanceModel) {
            return $performanceModel->getMetricsComparison();
        }, 300);

        $performanceData = Cache::remember('llm_performance_data', function() use ($performanceModel) {
            return $performanceModel->all();
        }, 300);

        // Build LLM data from real database
        $llmData = [];

        if (isset($performanceData['data']) && is_array($performanceData['data'])) {
            foreach ($performanceData['data'] as $model) {
                $modelName = $model['model_name'] ?? 'Unknown';
                $config = $this->modelConfig[$modelName] ?? [
                    'shortName' => strtolower($modelName),
                    'icon' => '🤖',
                    'color' => '#6366f1'
                ];

                $avgScore = (float)($model['overall_avg_score'] ?? 0);
                $totalResponses = (int)($model['total_responses'] ?? 0);
                $avgTime = (int)($model['avg_response_time'] ?? 0);

                $llmData[] = [
                    'name' => $modelName,
                    'shortName' => $config['shortName'],
                    'icon' => $config['icon'],
                    'color' => $config['color'],
                    'accuracy' => round($avgScore),
                    'responses' => $totalResponses,
                    'avgTime' => $avgTime > 0 ? round($avgTime / 1000, 1) . 's' : 'N/A',
                    'trend' => $this->calculateTrend($modelName),
                    'trendUp' => $this->isTrendUp($modelName),
                    'metrics' => [
                        'coherence' => round((float)($model['avg_coherence'] ?? 0)),
                        'consistency' => round((float)($model['avg_consistency'] ?? 0)),
                        'fluency' => round((float)($model['avg_fluency'] ?? 0)),
                        'relevance' => round((float)($model['avg_relevance'] ?? 0)),
                    ],
                ];
            }
        }

        // If no data from DB, use fallback from llm_models table
        if (empty($llmData)) {
            $llmData = $this->getFallbackLlmData();
        }

        // Get recent responses for activity feed with caching (1 minute)
        $recentResponses = Cache::remember('llm_recent_responses', function() use ($aiResponseModel) {
            return $aiResponseModel->all(10);
        }, 60);

        $this->render('llm-monitoring/index', [
            'pageTitle' => 'LLM Мониторинг',
            'currentPage' => 'llm-monitoring',
            'breadcrumb' => 'LLM Мониторинг',
            'llmData' => $llmData,
            'modelPerformance' => $modelPerformance,
            'recentResponses' => $recentResponses['data'] ?? [],
            'totalModels' => count($llmData),
            'totalEvaluations' => array_sum(array_column($llmData, 'responses')),
        ]);
    }

    private function calculateTrend(string $modelName): string
    {
        $db = new SupabaseClient();

        $weekAgo = date('Y-m-d\TH:i:s', strtotime('-7 days'));
        $twoWeeksAgo = date('Y-m-d\TH:i:s', strtotime('-14 days'));

        $currentResult = $db->from('evaluations')
            ->select('avg_score, ai_responses!inner(model_name)')
            ->gte('evaluated_at', $weekAgo)
            ->get();

        $previousResult = $db->from('evaluations')
            ->select('avg_score, ai_responses!inner(model_name)')
            ->gte('evaluated_at', $twoWeeksAgo)
            ->lt('evaluated_at', $weekAgo)
            ->get();

        $calcAvg = function(array $data, string $model) {
            $sum = 0;
            $count = 0;
            foreach ($data as $e) {
                if (($e['ai_responses']['model_name'] ?? '') === $model) {
                    $sum += (float)($e['avg_score'] ?? 0);
                    $count++;
                }
            }
            return $count > 0 ? $sum / $count : 0;
        };

        $currentAvg = $calcAvg($currentResult['data'] ?? [], $modelName);
        $previousAvg = $calcAvg($previousResult['data'] ?? [], $modelName);

        if ($previousAvg == 0 && $currentAvg == 0) return '0%';
        if ($previousAvg == 0) return '+100%';

        $percent = round((($currentAvg - $previousAvg) / $previousAvg) * 100);
        return ($percent >= 0 ? '+' : '') . $percent . '%';
    }

    private function isTrendUp(string $modelName): bool
    {
        $trend = $this->calculateTrend($modelName);
        return !str_starts_with($trend, '-');
    }

    private function getFallbackLlmData(): array
    {
        $db = new SupabaseClient();
        $result = $db->from('llm_models')
            ->select('*')
            ->eq('is_active', 'true')
            ->get();

        $llmData = [];
        if (isset($result['data']) && is_array($result['data'])) {
            foreach ($result['data'] as $model) {
                $llmData[] = [
                    'name' => $model['name'],
                    'shortName' => $model['short_name'],
                    'icon' => $model['icon'] ?? '🤖',
                    'color' => $model['color'] ?? '#6366f1',
                    'accuracy' => 0,
                    'responses' => 0,
                    'avgTime' => 'N/A',
                    'trend' => '+0%',
                    'trendUp' => true,
                    'metrics' => [
                        'coherence' => 0,
                        'consistency' => 0,
                        'fluency' => 0,
                        'relevance' => 0,
                    ],
                ];
            }
        }

        return $llmData;
    }

    public function exportCsv(): void
    {
        $performanceModel = new ModelPerformance();
        $performanceData = $performanceModel->all();

        $filename = 'llm_monitoring_' . date('Y-m-d') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        // BOM for Excel UTF-8
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        // Headers
        fputcsv($output, [
            'Модель',
            'Ответов',
            'Средний балл',
            'Coherence',
            'Consistency',
            'Fluency',
            'Relevance',
            'Среднее время (мс)'
        ]);

        // Data
        if (isset($performanceData['data']) && is_array($performanceData['data'])) {
            foreach ($performanceData['data'] as $model) {
                fputcsv($output, [
                    $model['model_name'] ?? '',
                    $model['total_responses'] ?? 0,
                    round((float)($model['overall_avg_score'] ?? 0), 2),
                    round((float)($model['avg_coherence'] ?? 0), 2),
                    round((float)($model['avg_consistency'] ?? 0), 2),
                    round((float)($model['avg_fluency'] ?? 0), 2),
                    round((float)($model['avg_relevance'] ?? 0), 2),
                    $model['avg_response_time'] ?? 0,
                ]);
            }
        }

        fclose($output);
        exit;
    }
}
