<?php

namespace App\Controllers;

class TrendsController extends BaseController
{
    public function index(): void
    {
        $stats = [
            'avgAccuracy' => 78,
            'processedRequests' => 2847,
            'problematicResponses' => 124,
            'newSources' => 89,
        ];

        $topProjects = [
            ['name' => 'Цифровой Казахстан', 'growth' => '+12%', 'icon' => '💻'],
            ['name' => 'Имидж Президента', 'growth' => '+10%', 'icon' => '🏛️'],
            ['name' => 'Закон и порядок', 'growth' => '+8%', 'icon' => '⚖️'],
            ['name' => 'Freedom Bank', 'growth' => '+7%', 'icon' => '🏦'],
            ['name' => 'Январь 2022', 'growth' => '+5%', 'icon' => '📅'],
        ];

        $this->render('trends/index', [
            'pageTitle' => 'Тренды и динамика',
            'currentPage' => 'trends',
            'breadcrumb' => 'Тренды',
            'stats' => $stats,
            'topProjects' => $topProjects,
        ]);
    }
}
