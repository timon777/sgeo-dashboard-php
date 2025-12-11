<?php

namespace App\Controllers;

use App\Models\ReferenceAnswer;
use App\Models\Evaluation;
use App\Models\AiResponse;

class TopicsController extends BaseController
{
    public function show(string $id): void
    {
        $referenceModel = new ReferenceAnswer();
        $evaluationModel = new Evaluation();
        $aiResponseModel = new AiResponse();

        // Get topic (reference answer)
        $topic = $referenceModel->find($id);

        if (!$topic) {
            http_response_code(404);
            include __DIR__ . '/../Views/errors/404.php';
            return;
        }

        // Get current tab from query parameter
        $currentTab = $_GET['tab'] ?? 'overview';

        // Get evaluations for this topic (reference_id)
        $evaluationsResult = $evaluationModel->byReferenceId($id, 100);
        $evaluations = $evaluationsResult['data'] ?? [];

        // Get AI responses for these evaluations
        $responses = [];
        $aiResponseIds = array_unique(array_column($evaluations, 'ai_response_id'));
        foreach ($aiResponseIds as $responseId) {
            $response = $aiResponseModel->find($responseId);
            if ($response) {
                $responses[$responseId] = $response;
            }
        }

        // Calculate statistics
        $stats = [
            'totalResponses' => count($evaluations),
            'avgScore' => 0,
            'sentimentStats' => [
                'positive' => 0,
                'neutral' => 0,
                'negative' => 0,
            ],
        ];

        if (!empty($evaluations)) {
            $totalScore = 0;
            foreach ($evaluations as $eval) {
                $totalScore += (float)($eval['avg_score'] ?? 0);
                $sentiment = strtolower($eval['sentiment'] ?? 'neutral');
                if (isset($stats['sentimentStats'][$sentiment])) {
                    $stats['sentimentStats'][$sentiment]++;
                }
            }
            $stats['avgScore'] = round($totalScore / count($evaluations), 1);
        }

        $this->render('topics/show', [
            'pageTitle' => $topic['topic'] ?? 'Топик',
            'currentPage' => 'topics',
            'breadcrumb' => 'Топик',
            'topic' => $topic,
            'currentTab' => $currentTab,
            'evaluations' => $evaluations,
            'responses' => $responses,
            'stats' => $stats,
        ]);
    }
}
