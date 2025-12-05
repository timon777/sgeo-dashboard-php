<?php

namespace App\Controllers;

use App\Models\AiResponse;
use App\Models\Evaluation;
use App\Services\SupabaseClient;

class PromptsController extends BaseController
{
    public function index(): void
    {
        $aiResponseModel = new AiResponse();
        $evaluationModel = new Evaluation();

        $responses = $aiResponseModel->all(50);
        $recentEvaluations = $evaluationModel->recentDetailed(50);

        // Build prompts array from database responses
        $prompts = [];
        if (isset($responses['data']) && is_array($responses['data'])) {
            foreach ($responses['data'] as $r) {
                // Determine LLM shortname
                $llm = $this->getLlmShortName($r['model_name'] ?? '');

                // Determine tone based on sentiment score or field
                $tone = $this->determineTone($r['sentiment_score'] ?? null, $r['tone'] ?? null);

                // Determine risk level
                $risk = $this->determineRisk($r['risk_score'] ?? null, $r['risk_level'] ?? null);

                $prompts[] = [
                    'prompt' => $r['prompt'] ?? '',
                    'llm' => $llm,
                    'response' => $r['response'] ?? '',
                    'tone' => $tone,
                    'score' => (int)($r['quality_score'] ?? $r['overall_score'] ?? 0),
                    'risk' => $risk,
                    'date' => $r['created_at'] ?? date('Y-m-d'),
                ];
            }
        }

        // Get total count
        $totalPrompts = count($responses['data'] ?? []);
        $stats = $aiResponseModel->getStats();
        if (isset($stats['total']) && $stats['total'] > 0) {
            $totalPrompts = $stats['total'];
        }

        $this->render('prompts/index', [
            'pageTitle' => 'Промты и ответы LLM',
            'currentPage' => 'prompts',
            'breadcrumb' => 'Промты',
            'prompts' => $prompts,
            'totalPrompts' => $totalPrompts,
            'dbResponses' => $responses['data'] ?? [],
            'dbEvaluations' => $recentEvaluations['data'] ?? [],
        ]);
    }

    private function getLlmShortName(string $modelName): string
    {
        $modelName = strtolower($modelName);

        if (strpos($modelName, 'gpt') !== false || strpos($modelName, 'chatgpt') !== false) {
            return 'gpt';
        }
        if (strpos($modelName, 'gemini') !== false) {
            return 'gemini';
        }
        if (strpos($modelName, 'deepseek') !== false) {
            return 'deepseek';
        }
        if (strpos($modelName, 'perplexity') !== false) {
            return 'perplexity';
        }
        if (strpos($modelName, 'grok') !== false) {
            return 'grok';
        }
        if (strpos($modelName, 'claude') !== false) {
            return 'claude';
        }

        return 'gpt'; // default
    }

    private function determineTone(?float $sentimentScore, ?string $toneField): string
    {
        if ($toneField) {
            return strtolower($toneField);
        }

        if ($sentimentScore !== null) {
            if ($sentimentScore > 0.3) {
                return 'positive';
            }
            if ($sentimentScore < -0.3) {
                return 'negative';
            }
        }

        return 'neutral';
    }

    private function determineRisk(?float $riskScore, ?string $riskLevel): string
    {
        if ($riskLevel) {
            return strtolower($riskLevel);
        }

        if ($riskScore !== null) {
            if ($riskScore >= 70) {
                return 'high';
            }
            if ($riskScore >= 40) {
                return 'medium';
            }
        }

        return 'low';
    }
}
