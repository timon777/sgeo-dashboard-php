<div class="page-header animate-on-scroll">
    <div class="page-header-top">
        <div class="page-title-group">
            <h1 class="page-title">Тренды и динамика</h1>
            <p class="page-subtitle">Аналитика изменений и тенденций в работе системы</p>
        </div>
        <div class="page-actions">
            <div class="tabs-container">
                <button class="tab active">24ч</button>
                <button class="tab">7д</button>
                <button class="tab">30д</button>
                <button class="tab">90д</button>
            </div>
        </div>
    </div>
</div>

<!-- Stats Grid -->
<div class="stats-grid stagger-children">
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
        </div>
        <div class="stat-content">
            <div class="stat-label">Средняя точность</div>
            <div class="stat-value"><?= $stats['avgAccuracy'] ?>%</div>
        </div>
        <div class="stat-description">По всем проектам</div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon-container green">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                </svg>
            </div>
        </div>
        <div class="stat-content">
            <div class="stat-label">Обработано запросов</div>
            <div class="stat-value"><?= number_format($stats['processedRequests']) ?></div>
        </div>
        <div class="stat-description">За выбранный период</div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon-container" style="background: var(--danger-bg); color: var(--danger);">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                    <line x1="12" y1="9" x2="12" y2="13"/>
                    <line x1="12" y1="17" x2="12.01" y2="17"/>
                </svg>
            </div>
        </div>
        <div class="stat-content">
            <div class="stat-label">Проблемных ответов</div>
            <div class="stat-value"><?= number_format($stats['problematicResponses']) ?></div>
        </div>
        <div class="stat-description">Требуют внимания</div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon-container purple">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                </svg>
            </div>
        </div>
        <div class="stat-content">
            <div class="stat-label">Новых источников</div>
            <div class="stat-value"><?= number_format($stats['newSources']) ?></div>
        </div>
        <div class="stat-description">За последний месяц</div>
    </div>
</div>

<!-- Main Trend Chart -->
<div class="chart-card" style="margin-bottom: 32px;">
    <div class="chart-header">
        <div>
            <div class="chart-title">Динамика точности по проектам</div>
            <div class="chart-description">Изменение показателей за выбранный период</div>
        </div>
    </div>
    <div class="chart-container" style="height: 350px;">
        <canvas id="mainTrendChart"></canvas>
    </div>
</div>

<!-- Secondary Charts -->
<div class="charts-section">
    <div class="chart-card">
        <div class="chart-header">
            <div>
                <div class="chart-title">Распределение по LLM</div>
                <div class="chart-description">Использование моделей за период</div>
            </div>
        </div>
        <div class="chart-container">
            <canvas id="llmDistributionChart"></canvas>
        </div>
    </div>

    <div class="chart-card">
        <div class="chart-header">
            <div>
                <div class="chart-title">Топ проектов по росту</div>
                <div class="chart-description">Наибольшее улучшение показателей</div>
            </div>
        </div>
        <div class="top-projects-list">
            <?php foreach ($topProjects as $index => $project): ?>
            <div class="top-project-item">
                <div class="top-project-rank"><?= $index + 1 ?></div>
                <div class="top-project-icon"><?= $project['icon'] ?></div>
                <div class="top-project-name"><?= htmlspecialchars($project['name']) ?></div>
                <div class="top-project-growth" style="color: var(--success);"><?= $project['growth'] ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<style>
.top-projects-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.top-project-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    background: rgba(255, 255, 255, 0.03);
    border-radius: var(--radius-md);
    transition: background var(--transition-fast);
}

.top-project-item:hover {
    background: rgba(255, 255, 255, 0.06);
}

.top-project-rank {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: var(--accent-gradient);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: 600;
    color: white;
}

.top-project-icon {
    font-size: 20px;
}

.top-project-name {
    flex: 1;
    font-size: 14px;
    font-weight: 500;
    color: var(--text-primary);
}

.top-project-growth {
    font-family: 'JetBrains Mono', monospace;
    font-size: 14px;
    font-weight: 600;
}
</style>

<?php
$pageScripts = <<<'SCRIPTS'
<script>
function initTrendsCharts() {
    const themeColors = getChartColors();
    updateChartDefaults();

    const colors = {
        primary: '#6366f1',
        secondary: '#8b5cf6',
        success: '#22c55e',
        warning: '#f59e0b',
        danger: '#ef4444'
    };

    // Main Trend Line Chart
    pageCharts.mainTrend = new Chart(document.getElementById('mainTrendChart'), {
        type: 'line',
        data: {
            labels: ['Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс'],
            datasets: [
                { label: 'Имидж Президента', data: [68, 70, 72, 74, 75, 77, 78], borderColor: colors.primary, backgroundColor: colors.primary + '20', fill: true, tension: 0.4, borderWidth: 2 },
                { label: 'Январь 2022', data: [65, 67, 68, 70, 71, 71, 72], borderColor: colors.success, backgroundColor: colors.success + '10', fill: true, tension: 0.4, borderWidth: 2 },
                { label: 'Цифровой Казахстан', data: [80, 81, 82, 83, 84, 84, 85], borderColor: colors.secondary, backgroundColor: colors.secondary + '10', fill: true, tension: 0.4, borderWidth: 2 },
                { label: 'АЭС', data: [72, 71, 70, 70, 69, 69, 69], borderColor: colors.warning, backgroundColor: colors.warning + '10', fill: true, tension: 0.4, borderWidth: 2 },
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom', labels: { padding: 20, usePointStyle: true, pointStyle: 'circle' } } },
            scales: {
                y: { beginAtZero: false, min: 60, max: 90, grid: { color: themeColors.grid }, ticks: { color: themeColors.text } },
                x: { grid: { display: false }, ticks: { color: themeColors.text } }
            },
            interaction: { mode: 'index', intersect: false }
        }
    });

    // LLM Distribution Bar Chart
    pageCharts.llmDist = new Chart(document.getElementById('llmDistributionChart'), {
        type: 'bar',
        data: {
            labels: ['ChatGPT', 'DeepSeek', 'Gemini', 'Grok', 'Perplexity'],
            datasets: [{
                label: 'Количество запросов',
                data: [1247, 856, 723, 634, 412],
                backgroundColor: [colors.success, colors.primary, colors.warning, colors.secondary, colors.danger],
                borderRadius: 8,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { color: themeColors.grid }, ticks: { color: themeColors.text } },
                x: { grid: { display: false }, ticks: { color: themeColors.text } }
            }
        }
    });
}

document.addEventListener('DOMContentLoaded', initTrendsCharts);
</script>
SCRIPTS;
?>
