<?php

namespace App\Controllers;

use App\Models\ModelPerformance;
use App\Models\AiResponse;
use App\Models\Evaluation;
use App\Services\SupabaseClient;

class LlmMonitoringController extends BaseController
{
    private array $llmConfig = [
        'gpt-4' => ['name' => 'GPT-4', 'shortName' => 'gpt', 'icon' => '🤖', 'color' => '#10a37f'],
        'gpt-3.5-turbo' => ['name' => 'GPT-3.5', 'shortName' => 'gpt', 'icon' => '🤖', 'color' => '#10a37f'],
        'chatgpt' => ['name' => 'ChatGPT', 'shortName' => 'gpt', 'icon' => '🤖', 'color' => '#10a37f'],
        'deepseek' => ['name' => 'DeepSeek', 'shortName' => 'deepseek', 'icon' => '🔍', 'color' => '#4d6bfe'],
        'grok' => ['name' => 'Grok', 'shortName' => 'grok', 'icon' => '⚡', 'color' => '#1d9bf0'],
        'gemini' => ['name' => 'Gemini', 'shortName' => 'gemini', 'icon' => '💎', 'color' => '#4285f4'],
        'gemini-pro' => ['name' => 'Gemini Pro', 'shortName' => 'gemini', 'icon' => '💎', 'color' => '#4285f4'],
        'perplexity' => ['name' => 'Perplexity', 'shortName' => 'perplexity', 'icon' => '🌐', 'color' => '#8b5cf6'],
        'claude' => ['name' => 'Claude', 'shortName' => 'claude', 'icon' => '🧠', 'color' => '#6366f1'],
    ];

    public function index(): void
    {
        $performanceModel = new ModelPerformance();
        $aiResponseModel = new AiResponse();

        $modelPerformance = $performanceModel->getMetricsComparison();

        // Build LLM data from database
        $llmData = [];
        foreach ($modelPerformance as $modelName => $metrics) {
            $config = $this->getModelConfig($modelName);

            $llmData[] = [
                'name' => $config['name'],
                'shortName' => $config['shortName'],
                'icon' => $config['icon'],
                'color' => $config['color'],
                'accuracy' => round($metrics['overall'] ?? 0),
                'responses' => $metrics['total_evaluations'] ?? 0,
                'avgTime' => $this->formatAvgTime($metrics['avg_response_time'] ?? null),
                'trend' => $this->formatTrend($metrics['trend_percent'] ?? null),
                'trendUp' => ($metrics['trend_percent'] ?? 0) >= 0,
            ];
        }

        // Sort by accuracy descending
        usort($llmData, function ($a, $b) {
            return $b['accuracy'] <=> $a['accuracy'];
        });

        $this->render('llm-monitoring/index', [
            'pageTitle' => 'LLM Мониторинг',
            'currentPage' => 'llm-monitoring',
            'breadcrumb' => 'LLM Мониторинг',
            'llmData' => $llmData,
            'modelPerformance' => $modelPerformance,
        ]);
    }

    private function getModelConfig(string $modelName): array
    {
        $modelName = strtolower($modelName);

        foreach ($this->llmConfig as $key => $config) {
            if (strpos($modelName, $key) !== false) {
                return $config;
            }
        }

        // Default config
        return [
            'name' => ucfirst($modelName),
            'shortName' => 'other',
            'icon' => '🤖',
            'color' => '#6366f1',
        ];
    }

    private function formatAvgTime(?float $seconds): string
    {
        if ($seconds === null) {
            return '-';
        }
        return round($seconds, 1) . 's';
    }

    private function formatTrend(?float $percent): string
    {
        if ($percent === null) {
            return '0%';
        }
        $sign = $percent >= 0 ? '+' : '';
        return $sign . round($percent) . '%';
    }
}
