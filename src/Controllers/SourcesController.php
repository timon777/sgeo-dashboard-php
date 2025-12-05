<?php

namespace App\Controllers;

use App\Models\Source;

class SourcesController extends BaseController
{
    public function index(): void
    {
        $sourceModel = new Source();
        $result = $sourceModel->all(100);

        $sources = [];
        if (isset($result['data']) && is_array($result['data'])) {
            foreach ($result['data'] as $s) {
                $sources[] = [
                    'domain' => $s['domain'],
                    'type' => $s['type'],
                    'country' => $s['country'],
                    'expertise' => (int)$s['expertise_score'],
                    'experience' => (int)$s['experience_score'],
                    'authority' => (int)$s['authority_score'],
                    'trust' => (int)$s['trust_score'],
                    'eeat' => (int)$s['eeat_combined'],
                    'share' => (float)$s['share_percent'],
                    'author' => (bool)$s['has_author'],
                    'https' => (bool)$s['has_https'],
                ];
            }
        }

        $totalSources = $sourceModel->count();

        $this->render('sources/index', [
            'pageTitle' => 'Источники информации',
            'currentPage' => 'sources',
            'breadcrumb' => 'Источники',
            'sources' => $sources,
            'totalSources' => $totalSources,
        ]);
    }

    public function exportCsv(): void
    {
        $sourceModel = new Source();
        $result = $sourceModel->all(1000);

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="sources_' . date('Y-m-d') . '.csv"');

        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        fputcsv($output, ['Домен', 'Тип', 'Страна', 'Экспертиза', 'Опыт', 'Авторитетность', 'Доверие', 'E-E-A-T', 'Доля (%)', 'Автор', 'HTTPS']);

        if (isset($result['data'])) {
            foreach ($result['data'] as $s) {
                fputcsv($output, [
                    $s['domain'],
                    $s['type'],
                    $s['country'],
                    $s['expertise_score'],
                    $s['experience_score'],
                    $s['authority_score'],
                    $s['trust_score'],
                    $s['eeat_combined'],
                    $s['share_percent'],
                    $s['has_author'] ? 'Да' : 'Нет',
                    $s['has_https'] ? 'Да' : 'Нет'
                ]);
            }
        }

        fclose($output);
    }
}
