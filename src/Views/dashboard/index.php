<div class="page-header animate-on-scroll">
    <div class="page-header-top">
        <div class="page-title-group">
            <h1 class="page-title">Аналитический дашборд</h1>
            <p class="page-subtitle">Мониторинг качества ответов LLM, анализ нарративов и оценка источников информации</p>
        </div>
        <div class="page-actions">
            <a href="/export/projects" class="btn btn-secondary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                    <polyline points="7,10 12,15 17,10"/>
                    <line x1="12" y1="15" x2="12" y2="3"/>
                </svg>
                Экспорт
            </a>
            <a href="/projects" class="btn btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="5" x2="12" y2="19"/>
                    <line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                Новый проект
            </a>
        </div>
    </div>

    <div class="tabs-container">
        <a href="/" class="tab active">Обзор</a>
        <a href="/projects" class="tab">По проектам</a>
        <a href="/llm-monitoring" class="tab">По LLM</a>
        <a href="/sources" class="tab">По источникам</a>
    </div>
</div>

<!-- Stats Grid -->
<div class="stats-grid stagger-children">
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon-container blue">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/>
                </svg>
            </div>
            <div class="stat-trend <?= ($trends['projects']['up'] ?? true) ? 'up' : 'down' ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="<?= ($trends['projects']['up'] ?? true) ? 'm18 15-6-6-6 6' : 'm6 9 6 6 6-6' ?>"/>
                </svg>
                <?= $trends['projects']['value'] ?? '0%' ?>
            </div>
        </div>
        <div class="stat-content">
            <div class="stat-label">Активных проектов</div>
            <div class="stat-value"><?= $stats['activeProjects'] ?></div>
        </div>
        <div class="stat-description">За последние 7 дней</div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon-container green">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                </svg>
            </div>
            <div class="stat-trend <?= ($trends['prompts']['up'] ?? true) ? 'up' : 'down' ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="<?= ($trends['prompts']['up'] ?? true) ? 'm18 15-6-6-6 6' : 'm6 9 6 6 6-6' ?>"/>
                </svg>
                <?= $trends['prompts']['value'] ?? '0%' ?>
            </div>
        </div>
        <div class="stat-content">
            <div class="stat-label">Обработано промтов</div>
            <div class="stat-value"><?= number_format($stats['processedPrompts']) ?></div>
        </div>
        <div class="stat-description">За последние 7 дней</div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon-container purple">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                </svg>
            </div>
            <div class="stat-trend <?= ($trends['sources']['up'] ?? true) ? 'up' : 'down' ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="<?= ($trends['sources']['up'] ?? true) ? 'm18 15-6-6-6 6' : 'm6 9 6 6 6-6' ?>"/>
                </svg>
                <?= $trends['sources']['value'] ?? '0%' ?>
            </div>
        </div>
        <div class="stat-content">
            <div class="stat-label">Проанализировано источников</div>
            <div class="stat-value"><?= number_format($stats['analyzedSources']) ?></div>
        </div>
        <div class="stat-description">За последние 7 дней</div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon-container orange">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 2v4"/>
                    <path d="m16.24 7.76 2.83-2.83"/>
                    <path d="M22 12h-4"/>
                    <path d="m16.24 16.24 2.83 2.83"/>
                    <path d="M12 22v-4"/>
                    <path d="m7.76 16.24-2.83 2.83"/>
                    <path d="M2 12h4"/>
                    <path d="m7.76 7.76-2.83-2.83"/>
                </svg>
            </div>
            <div class="stat-trend <?= ($trends['accuracy']['up'] ?? true) ? 'up' : 'down' ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="<?= ($trends['accuracy']['up'] ?? true) ? 'm18 15-6-6-6 6' : 'm6 9 6 6 6-6' ?>"/>
                </svg>
                <?= $trends['accuracy']['value'] ?? '0%' ?>
            </div>
        </div>
        <div class="stat-content">
            <div class="stat-label">Средняя точность</div>
            <div class="stat-value"><?= $stats['averageAccuracy'] ?>%</div>
        </div>
        <div class="stat-description">За последние 7 дней</div>
    </div>
</div>

<!-- Charts Section -->
<div class="charts-section charts-section-single">
    <div class="chart-card chart-card-wide">
        <div class="chart-header">
            <div>
                <div class="chart-title">Динамика точности по проектам</div>
                <div class="chart-description">Изменение средней точности ответов за последние 7 дней</div>
            </div>
        </div>
        <div class="chart-container chart-container-wide">
            <canvas id="accuracyDynamicsChart"></canvas>
        </div>
        <?php if (!empty($accuracyDynamics['datasets'])): ?>
        <div class="legend" style="margin-top: 16px; justify-content: center;">
            <?php foreach ($accuracyDynamics['datasets'] as $dataset): ?>
            <div class="legend-item">
                <span class="legend-dot" style="background: <?= $dataset['color'] ?>;"></span>
                <span><?= htmlspecialchars($dataset['label']) ?></span>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Analysis Grid -->
<div class="analysis-grid">
    <div class="analysis-card">
        <div class="analysis-title">Качество промтов</div>
        <div class="analysis-subtitle">Оценка формулировки запросов</div>
        <div class="radar-container">
            <canvas id="promptsRadarChart"></canvas>
        </div>
        <div class="legend">
            <div class="legend-item">
                <span class="legend-dot" style="background: #6366f1;"></span>
                <span>Средний балл: <?= $radarData['prompts']['avg'] ?? 0 ?></span>
            </div>
        </div>
    </div>

    <div class="analysis-card">
        <div class="analysis-title">Качество ответов</div>
        <div class="analysis-subtitle">Комплексная оценка генерации</div>
        <div class="radar-container">
            <canvas id="answersRadarChart"></canvas>
        </div>
        <div class="legend">
            <div class="legend-item">
                <span class="legend-dot" style="background: #22c55e;"></span>
                <span>Средний балл: <?= $radarData['answers']['avg'] ?? 0 ?></span>
            </div>
        </div>
    </div>

    <div class="analysis-card">
        <div class="analysis-title">E-E-A-T оценка источников</div>
        <div class="analysis-subtitle">Опыт, Экспертиза, Авторитетность, Надёжность</div>
        <div class="radar-container">
            <canvas id="eeatRadarChart"></canvas>
        </div>
        <div class="legend">
            <div class="legend-item">
                <span class="legend-dot" style="background: #8b5cf6;"></span>
                <span>Средний балл: <?= $radarData['eeat']['avg'] ?? 0 ?></span>
            </div>
        </div>
    </div>
</div>

<!-- FAQ Section -->
<div class="accordion-section">
    <h2 class="section-title">Часто задаваемые вопросы</h2>
    <div class="accordion">
        <div class="accordion-item active">
            <div class="accordion-header" onclick="toggleAccordion(this)">
                <span class="accordion-title">Что такое SGEO и как работает система мониторинга?</span>
                <svg class="accordion-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="m6 9 6 6 6-6"/>
                </svg>
            </div>
            <div class="accordion-content">
                <div class="accordion-body">
                    SGEO — это платформа аналитического мониторинга LLM-систем, которая отслеживает качество ответов крупнейших языковых моделей (ChatGPT, Claude, Gemini, Perplexity) по заданным тематикам и нарративам. Система автоматически анализирует точность, тональность и соответствие ответов целевым параметрам.
                </div>
            </div>
        </div>

        <div class="accordion-item">
            <div class="accordion-header" onclick="toggleAccordion(this)">
                <span class="accordion-title">Как оценивается качество промтов и ответов?</span>
                <svg class="accordion-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="m6 9 6 6 6-6"/>
                </svg>
            </div>
            <div class="accordion-content">
                <div class="accordion-body">
                    Качество оценивается по множеству параметров: конкретность формулировки, полнота задания, нейтральность, однозначность интерпретации. Для ответов дополнительно анализируются соответствие запросу, фактическая точность и ясность изложения.
                </div>
            </div>
        </div>

        <div class="accordion-item">
            <div class="accordion-header" onclick="toggleAccordion(this)">
                <span class="accordion-title">Что означает E-E-A-T оценка источников?</span>
                <svg class="accordion-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="m6 9 6 6 6-6"/>
                </svg>
            </div>
            <div class="accordion-content">
                <div class="accordion-body">
                    E-E-A-T (Experience, Expertise, Authoritativeness, Trustworthiness) — это методология Google для оценки качества контента. Мы адаптировали её для анализа источников, цитируемых LLM: оцениваем опыт автора, уровень экспертизы, авторитетность издания и общую надёжность информации.
                </div>
            </div>
        </div>

        <div class="accordion-item">
            <div class="accordion-header" onclick="toggleAccordion(this)">
                <span class="accordion-title">Как часто обновляются данные мониторинга?</span>
                <svg class="accordion-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="m6 9 6 6 6-6"/>
                </svg>
            </div>
            <div class="accordion-content">
                <div class="accordion-body">
                    Базовые метрики обновляются ежедневно в автоматическом режиме. Глубокий анализ нарративов и качества источников проводится еженедельно. Критические изменения в тональности или появление рисковых паттернов отслеживаются в реальном времени с уведомлениями.
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Sources Section -->
<h2 class="section-title">Распределение источников</h2>
<div class="sources-section">
    <div class="source-card">
        <div class="source-title">География источников</div>
        <div class="source-subtitle">Распределение по странам происхождения</div>
        <div class="pie-container">
            <canvas id="geoPieChart"></canvas>
        </div>
        <div class="legend">
            <?php
            $geoColors = ['#6366f1', '#22c55e', '#f59e0b', '#8b5cf6'];
            $geoLabels = $sourceStats['geography']['labels'] ?? [];
            $geoData = $sourceStats['geography']['data'] ?? [];
            foreach ($geoLabels as $i => $label):
            ?>
            <div class="legend-item">
                <span class="legend-dot" style="background: <?= $geoColors[$i] ?? '#6366f1' ?>;"></span>
                <span><?= $geoData[$i] ?? 0 ?>% — <?= htmlspecialchars($label) ?></span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="source-card">
        <div class="source-title">Типология источников</div>
        <div class="source-subtitle">Распределение по характеру площадок</div>
        <div class="pie-container">
            <canvas id="typesPieChart"></canvas>
        </div>
        <div class="legend">
            <?php
            $typeColors = ['#6366f1', '#22c55e', '#f59e0b', '#8b5cf6', '#ec4899'];
            $typeLabels = $sourceStats['types']['labels'] ?? [];
            $typeData = $sourceStats['types']['data'] ?? [];
            foreach ($typeLabels as $i => $label):
            ?>
            <div class="legend-item">
                <span class="legend-dot" style="background: <?= $typeColors[$i] ?? '#6366f1' ?>;"></span>
                <span><?= $typeData[$i] ?? 0 ?>% — <?= htmlspecialchars($label) ?></span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="source-card">
        <div class="source-title">Распределение по LLM</div>
        <div class="source-subtitle">Использование источников моделями</div>
        <div class="pie-container">
            <canvas id="llmPieChart"></canvas>
        </div>
        <div class="legend">
            <?php
            $llmColors = ['#6366f1', '#22c55e', '#f59e0b', '#8b5cf6', '#ec4899', '#ef4444'];
            $llmLabels = $sourceStats['llm']['labels'] ?? [];
            $llmData = $sourceStats['llm']['data'] ?? [];
            if (empty($llmLabels)):
            ?>
            <div class="legend-item">
                <span class="legend-dot" style="background: #6366f1;"></span>
                <span>Нет данных</span>
            </div>
            <?php else: ?>
            <?php foreach ($llmLabels as $i => $label): ?>
            <div class="legend-item">
                <span class="legend-dot" style="background: <?= $llmColors[$i] ?? '#6366f1' ?>;"></span>
                <span><?= $llmData[$i] ?? 0 ?>% — <?= htmlspecialchars($label) ?></span>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
// Pass PHP data to JavaScript
$jsData = [
    'projectData' => $projectData ?? [],
    'llmData' => $llmData ?? [],
    'trends' => $trends ?? [],
    'modelPerformance' => $modelPerformance ?? [],
    'radarData' => $radarData ?? [],
    'sourceStats' => $sourceStats ?? [],
    'accuracyDynamics' => $accuracyDynamics ?? [],
];
?>
<script>
const dashboardData = <?= json_encode($jsData, JSON_UNESCAPED_UNICODE) ?>;
</script>
<?php
$pageScripts = <<<'SCRIPTS'
<script>
// Dashboard Charts
function initDashboardCharts() {
    const themeColors = getChartColors();
    updateChartDefaults();

    const colors = {
        primary: '#6366f1',
        secondary: '#8b5cf6',
        success: '#22c55e',
        warning: '#f59e0b',
        danger: '#ef4444',
        pink: '#ec4899'
    };

    // Line Chart - Accuracy Dynamics by Project
    const dynamicsData = dashboardData.accuracyDynamics || { labels: [], datasets: [] };
    const dynamicsDatasets = (dynamicsData.datasets || []).map(ds => ({
        label: ds.label,
        data: ds.data,
        borderColor: ds.color,
        backgroundColor: ds.color + '20',
        borderWidth: 2,
        tension: 0.4,
        fill: false,
        pointRadius: 4,
        pointHoverRadius: 6,
        pointBackgroundColor: ds.color,
        pointBorderColor: isLightTheme() ? '#fff' : '#1a1c22',
        pointBorderWidth: 2,
    }));

    // Calculate dynamic y-axis bounds
    let allValues = [];
    (dynamicsData.datasets || []).forEach(ds => {
        if (ds.data) allValues = allValues.concat(ds.data);
    });
    const minVal = allValues.length > 0 ? Math.min(...allValues) : 0;
    const maxVal = allValues.length > 0 ? Math.max(...allValues) : 100;
    const padding = 5;
    const yMin = Math.max(0, Math.floor((minVal - padding) / 5) * 5);
    const yMax = Math.min(100, Math.ceil((maxVal + padding) / 5) * 5);

    new Chart(document.getElementById('accuracyDynamicsChart'), {
        type: 'line',
        data: {
            labels: dynamicsData.labels.length > 0 ? dynamicsData.labels : ['Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс'],
            datasets: dynamicsDatasets.length > 0 ? dynamicsDatasets : [{
                label: 'Нет данных',
                data: [0, 0, 0, 0, 0, 0, 0],
                borderColor: colors.primary,
                backgroundColor: colors.primary + '20',
                borderWidth: 2,
                tension: 0.4,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                intersect: false,
                mode: 'index'
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: isLightTheme() ? '#fff' : '#1a1c22',
                    titleColor: themeColors.textStrong,
                    bodyColor: themeColors.text,
                    borderColor: themeColors.grid,
                    borderWidth: 1,
                    padding: 12,
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + context.parsed.y + '%';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: false,
                    min: yMin,
                    max: yMax,
                    grid: { color: themeColors.grid },
                    ticks: {
                        color: themeColors.text,
                        callback: function(value) { return value + '%'; }
                    }
                },
                x: {
                    grid: { display: false },
                    ticks: { color: themeColors.text }
                }
            }
        }
    });

    // Radar Charts
    const radarOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            r: {
                beginAtZero: true,
                max: 100,
                ticks: { stepSize: 20, backdropColor: 'transparent', color: themeColors.text },
                grid: { color: themeColors.gridStrong },
                angleLines: { color: themeColors.gridStrong },
                pointLabels: { font: { size: 10 }, color: themeColors.textStrong }
            }
        }
    };

    // Prompts Radar Chart (from real data)
    const promptsRadarData = dashboardData.radarData.prompts || {labels: [], data: []};
    new Chart(document.getElementById('promptsRadarChart'), {
        type: 'radar',
        data: {
            labels: promptsRadarData.labels,
            datasets: [{
                data: promptsRadarData.data,
                backgroundColor: 'rgba(99, 102, 241, 0.2)',
                borderColor: colors.primary,
                borderWidth: 2,
                pointBackgroundColor: colors.primary,
                pointBorderColor: isLightTheme() ? '#1a1c22' : '#fff',
                pointBorderWidth: 1,
                pointRadius: 4
            }]
        },
        options: radarOptions
    });

    // Answers Radar Chart (from real data)
    const answersRadarData = dashboardData.radarData.answers || {labels: [], data: []};
    new Chart(document.getElementById('answersRadarChart'), {
        type: 'radar',
        data: {
            labels: answersRadarData.labels,
            datasets: [{
                data: answersRadarData.data,
                backgroundColor: 'rgba(34, 197, 94, 0.2)',
                borderColor: colors.success,
                borderWidth: 2,
                pointBackgroundColor: colors.success,
                pointBorderColor: isLightTheme() ? '#1a1c22' : '#fff',
                pointBorderWidth: 1,
                pointRadius: 4
            }]
        },
        options: radarOptions
    });

    // EEAT Radar Chart (from real data)
    const eeatRadarData = dashboardData.radarData.eeat || {labels: [], data: []};
    new Chart(document.getElementById('eeatRadarChart'), {
        type: 'radar',
        data: {
            labels: eeatRadarData.labels,
            datasets: [{
                data: eeatRadarData.data,
                backgroundColor: 'rgba(139, 92, 246, 0.2)',
                borderColor: colors.secondary,
                borderWidth: 2,
                pointBackgroundColor: colors.secondary,
                pointBorderColor: isLightTheme() ? '#1a1c22' : '#fff',
                pointBorderWidth: 1,
                pointRadius: 4
            }]
        },
        options: radarOptions
    });

    // Pie Charts
    const pieOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        cutout: '65%'
    };

    // Geography Pie Chart (from real data)
    const geoData = dashboardData.sourceStats.geography || {labels: [], data: []};
    new Chart(document.getElementById('geoPieChart'), {
        type: 'doughnut',
        data: {
            labels: geoData.labels,
            datasets: [{ data: geoData.data, backgroundColor: [colors.primary, colors.success, colors.warning, colors.secondary], borderWidth: 0 }]
        },
        options: pieOptions
    });

    // Types Pie Chart (from real data)
    const typesData = dashboardData.sourceStats.types || {labels: [], data: []};
    new Chart(document.getElementById('typesPieChart'), {
        type: 'doughnut',
        data: {
            labels: typesData.labels,
            datasets: [{ data: typesData.data, backgroundColor: [colors.primary, colors.success, colors.warning, colors.secondary, colors.pink], borderWidth: 0 }]
        },
        options: pieOptions
    });

    // LLM Distribution Pie Chart (from real data)
    const llmDistData = dashboardData.sourceStats.llm || {labels: [], data: []};
    const llmDistColors = [colors.primary, colors.success, colors.warning, colors.secondary, colors.pink, colors.danger];
    new Chart(document.getElementById('llmPieChart'), {
        type: 'doughnut',
        data: {
            labels: llmDistData.labels.length > 0 ? llmDistData.labels : ['Нет данных'],
            datasets: [{
                data: llmDistData.data.length > 0 ? llmDistData.data : [100],
                backgroundColor: llmDistColors.slice(0, Math.max(llmDistData.labels.length, 1)),
                borderWidth: 0
            }]
        },
        options: pieOptions
    });
}

document.addEventListener('DOMContentLoaded', initDashboardCharts);
</script>
SCRIPTS;
?>
