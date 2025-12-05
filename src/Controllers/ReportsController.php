<?php

namespace App\Controllers;

use App\Models\Report;

class ReportsController extends BaseController
{
    public function index(): void
    {
        $reportModel = new Report();

        // Получаем шаблоны отчётов
        $templatesResult = $reportModel->getTemplates();
        $reportTypes = [];
        if (isset($templatesResult['data'])) {
            foreach ($templatesResult['data'] as $t) {
                $reportTypes[] = [
                    'id' => $t['type'],
                    'title' => $t['name'],
                    'description' => $t['description'],
                    'icon' => $t['icon'],
                    'lastGenerated' => null,
                ];
            }
        }

        // Получаем последние отчёты
        $reportsResult = $reportModel->completed(10);
        $recentReports = [];
        if (isset($reportsResult['data'])) {
            foreach ($reportsResult['data'] as $r) {
                $recentReports[] = [
                    'name' => $r['name'],
                    'date' => $r['generated_at'] ? date('Y-m-d', strtotime($r['generated_at'])) : null,
                    'size' => $r['file_size_bytes'] ? round($r['file_size_bytes'] / 1024 / 1024, 1) . ' MB' : '-',
                ];
            }
        }

        $this->render('reports/index', [
            'pageTitle' => 'Отчёты',
            'currentPage' => 'reports',
            'breadcrumb' => 'Отчёты',
            'reportTypes' => $reportTypes,
            'recentReports' => $recentReports,
        ]);
    }
}
