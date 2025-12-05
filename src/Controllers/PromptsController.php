<?php

namespace App\Controllers;

use App\Models\AiResponse;
use App\Models\Evaluation;

class PromptsController extends BaseController
{
    public function index(): void
    {
        $aiResponseModel = new AiResponse();
        $evaluationModel = new Evaluation();

        $responses = $aiResponseModel->all(50);
        $recentEvaluations = $evaluationModel->recentDetailed(50);

        // Sample prompts data (in production, this comes from the database)
        $prompts = [
            [
                'prompt' => 'Расскажите о реформах президента Токаева в Казахстане',
                'llm' => 'gpt',
                'response' => 'Касым-Жомарт Токаев провёл масштабные политические и экономические реформы...',
                'tone' => 'positive',
                'score' => 85,
                'risk' => 'low',
                'date' => '2024-01-15',
            ],
            [
                'prompt' => 'Что произошло в Казахстане в январе 2022 года?',
                'llm' => 'gemini',
                'response' => 'В январе 2022 года в Казахстане произошли массовые протесты...',
                'tone' => 'neutral',
                'score' => 72,
                'risk' => 'medium',
                'date' => '2024-01-14',
            ],
            [
                'prompt' => 'Каковы перспективы цифровизации в Казахстане?',
                'llm' => 'deepseek',
                'response' => 'Казахстан активно развивает цифровую экономику через программу...',
                'tone' => 'positive',
                'score' => 88,
                'risk' => 'low',
                'date' => '2024-01-13',
            ],
            [
                'prompt' => 'Как оценивается строительство АЭС в Казахстане?',
                'llm' => 'perplexity',
                'response' => 'Вопрос строительства атомной электростанции остается дискуссионным...',
                'tone' => 'neutral',
                'score' => 65,
                'risk' => 'high',
                'date' => '2024-01-12',
            ],
            [
                'prompt' => 'Расскажите о Freedom Bank в Казахстане',
                'llm' => 'grok',
                'response' => 'Freedom Bank - один из ведущих цифровых банков Казахстана...',
                'tone' => 'positive',
                'score' => 91,
                'risk' => 'low',
                'date' => '2024-01-11',
            ],
        ];

        $this->render('prompts/index', [
            'pageTitle' => 'Промты и ответы LLM',
            'currentPage' => 'prompts',
            'breadcrumb' => 'Промты',
            'prompts' => $prompts,
            'totalPrompts' => 847,
            'dbResponses' => $responses['data'] ?? [],
            'dbEvaluations' => $recentEvaluations['data'] ?? [],
        ]);
    }
}
