<?php

namespace App\Controllers;

use App\Models\ModelPerformance;
use App\Models\AiResponse;
use App\Models\Evaluation;

class LlmMonitoringController extends BaseController
{
    public function index(): void
    {
        $performanceModel = new ModelPerformance();
        $aiResponseModel = new AiResponse();

        $modelPerformance = $performanceModel->getMetricsComparison();

        // LLM data for display
        $llmData = [
            [
                'name' => 'ChatGPT',
                'shortName' => 'gpt',
                'icon' => '🤖',
                'color' => '#10a37f',
                'accuracy' => 92,
                'responses' => 1247,
                'avgTime' => '1.2s',
                'trend' => '+5%',
                'trendUp' => true,
            ],
            [
                'name' => 'DeepSeek',
                'shortName' => 'deepseek',
                'icon' => '🔍',
                'color' => '#4d6bfe',
                'accuracy' => 88,
                'responses' => 856,
                'avgTime' => '0.8s',
                'trend' => '+12%',
                'trendUp' => true,
            ],
            [
                'name' => 'Grok',
                'shortName' => 'grok',
                'icon' => '⚡',
                'color' => '#1d9bf0',
                'accuracy' => 85,
                'responses' => 634,
                'avgTime' => '1.5s',
                'trend' => '+8%',
                'trendUp' => true,
            ],
            [
                'name' => 'Gemini',
                'shortName' => 'gemini',
                'icon' => '💎',
                'color' => '#4285f4',
                'accuracy' => 87,
                'responses' => 723,
                'avgTime' => '1.1s',
                'trend' => '-2%',
                'trendUp' => false,
            ],
            [
                'name' => 'Perplexity',
                'shortName' => 'perplexity',
                'icon' => '🌐',
                'color' => '#8b5cf6',
                'accuracy' => 84,
                'responses' => 412,
                'avgTime' => '2.1s',
                'trend' => '+3%',
                'trendUp' => true,
            ],
        ];

        $this->render('llm-monitoring/index', [
            'pageTitle' => 'LLM Мониторинг',
            'currentPage' => 'llm-monitoring',
            'breadcrumb' => 'LLM Мониторинг',
            'llmData' => $llmData,
            'modelPerformance' => $modelPerformance,
        ]);
    }
}
