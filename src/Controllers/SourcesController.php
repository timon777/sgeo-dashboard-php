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
}
