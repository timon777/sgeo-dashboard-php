<div class="page-header animate-on-scroll">
    <div class="page-header-top">
        <div class="page-title-group">
            <h1 class="page-title">LLM Мониторинг</h1>
            <p class="page-subtitle">Анализ производительности и качества ответов языковых моделей</p>
        </div>
        <div class="page-actions">
            <button class="btn btn-secondary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                    <polyline points="7,10 12,15 17,10"/>
                    <line x1="12" y1="15" x2="12" y2="3"/>
                </svg>
                Экспорт
            </button>
            <button class="btn btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Обновить данные
            </button>
        </div>
    </div>
</div>

<!-- LLM Cards Grid -->
<div class="llm-grid stagger-children">
    <?php foreach ($llmData as $llm): ?>
    <div class="llm-card">
        <div class="llm-header">
            <div class="llm-icon" style="background: <?= $llm['color'] ?>20;">
                <span style="font-size: 28px;"><?= $llm['icon'] ?></span>
            </div>
            <div class="llm-badge <?= $llm['shortName'] ?>"><?= $llm['shortName'] ?></div>
        </div>
        <h3 class="llm-name"><?= $llm['name'] ?></h3>
        <div class="llm-stats">
            <div class="llm-stat">
                <div class="llm-stat-label">Точность</div>
                <div class="llm-stat-value"><?= $llm['accuracy'] ?>%</div>
            </div>
            <div class="llm-stat">
                <div class="llm-stat-label">Ответов</div>
                <div class="llm-stat-value"><?= number_format($llm['responses']) ?></div>
            </div>
            <div class="llm-stat">
                <div class="llm-stat-label">Ср. время</div>
                <div class="llm-stat-value"><?= $llm['avgTime'] ?></div>
            </div>
        </div>
        <div class="llm-footer">
            <div class="progress-bar">
                <div class="progress-fill" style="width: <?= $llm['accuracy'] ?>%; background: <?= $llm['color'] ?>;"></div>
            </div>
            <div class="trend-badge <?= $llm['trendUp'] ? 'up' : 'down' ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="<?= $llm['trendUp'] ? 'm18 15-6-6-6 6' : 'm6 9 6 6 6-6' ?>"/>
                </svg>
                <?= $llm['trend'] ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Comparison Charts -->
<div class="charts-section">
    <div class="chart-card">
        <div class="chart-header">
            <div>
                <div class="chart-title">Сравнение точности по моделям</div>
                <div class="chart-description">Процент корректных ответов за последний месяц</div>
            </div>
        </div>
        <div class="chart-container">
            <canvas id="accuracyComparisonChart"></canvas>
        </div>
    </div>

    <div class="chart-card">
        <div class="chart-header">
            <div>
                <div class="chart-title">Динамика использования</div>
                <div class="chart-description">Количество запросов по дням</div>
            </div>
        </div>
        <div class="chart-container">
            <canvas id="usageTrendChart"></canvas>
        </div>
    </div>
</div>

<!-- Quality Metrics -->
<div class="analysis-grid">
    <div class="analysis-card">
        <div class="analysis-title">Метрики качества</div>
        <div class="analysis-subtitle">Оценка ответов по ключевым параметрам</div>
        <div class="radar-container">
            <canvas id="qualityRadarChart"></canvas>
        </div>
    </div>

    <div class="analysis-card">
        <div class="analysis-title">Распределение по тональности</div>
        <div class="analysis-subtitle">Анализ эмоциональной окраски ответов</div>
        <div class="pie-container">
            <canvas id="tonePieChart"></canvas>
        </div>
        <div class="legend">
            <div class="legend-item">
                <span class="legend-dot" style="background: #22c55e;"></span>
                <span>68% — Позитивная</span>
            </div>
            <div class="legend-item">
                <span class="legend-dot" style="background: #6366f1;"></span>
                <span>24% — Нейтральная</span>
            </div>
            <div class="legend-item">
                <span class="legend-dot" style="background: #ef4444;"></span>
                <span>8% — Негативная</span>
            </div>
        </div>
    </div>

    <div class="analysis-card">
        <div class="analysis-title">Уровни риска</div>
        <div class="analysis-subtitle">Классификация ответов по риску</div>
        <div class="pie-container">
            <canvas id="riskPieChart"></canvas>
        </div>
        <div class="legend">
            <div class="legend-item">
                <span class="legend-dot" style="background: #22c55e;"></span>
                <span>75% — Низкий</span>
            </div>
            <div class="legend-item">
                <span class="legend-dot" style="background: #f59e0b;"></span>
                <span>18% — Средний</span>
            </div>
            <div class="legend-item">
                <span class="legend-dot" style="background: #ef4444;"></span>
                <span>7% — Высокий</span>
            </div>
        </div>
    </div>
</div>

<style>
.llm-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 24px;
    margin-bottom: 32px;
}

.llm-card {
    background: var(--bg-card);
    border: 1px solid var(--border-subtle);
    border-radius: var(--radius-xl);
    padding: 24px;
    transition: all var(--transition-base);
}

.llm-card:hover {
    border-color: var(--border-medium);
    transform: translateY(-4px);
}

.llm-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 16px;
}

.llm-icon {
    width: 56px;
    height: 56px;
    border-radius: var(--radius-md);
    display: flex;
    align-items: center;
    justify-content: center;
}

.llm-name {
    font-size: 18px;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 16px;
}

.llm-stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 12px;
    margin-bottom: 16px;
}

.llm-stat {
    text-align: center;
    padding: 12px;
    background: rgba(255, 255, 255, 0.03);
    border-radius: var(--radius-md);
}

.llm-stat-label {
    font-size: 11px;
    color: var(--text-tertiary);
    margin-bottom: 4px;
}

.llm-stat-value {
    font-size: 16px;
    font-weight: 600;
    color: var(--text-primary);
    font-family: 'JetBrains Mono', monospace;
}

.llm-footer {
    display: flex;
    align-items: center;
    gap: 16px;
}

.llm-footer .progress-bar {
    flex: 1;
}
</style>

<?php
$pageScripts = <<<'SCRIPTS'
<script>
function initLlmCharts() {
    const themeColors = getChartColors();
    updateChartDefaults();

    const colors = {
        primary: '#6366f1',
        success: '#22c55e',
        warning: '#f59e0b',
        danger: '#ef4444',
        gpt: '#10a37f',
        deepseek: '#4d6bfe',
        grok: '#1d9bf0',
        gemini: '#4285f4',
        perplexity: '#8b5cf6'
    };

    // Accuracy Comparison Bar Chart
    pageCharts.accuracy = new Chart(document.getElementById('accuracyComparisonChart'), {
        type: 'bar',
        data: {
            labels: ['ChatGPT', 'DeepSeek', 'Gemini', 'Grok', 'Perplexity'],
            datasets: [{
                label: 'Точность %',
                data: [92, 88, 87, 85, 84],
                backgroundColor: [colors.gpt, colors.deepseek, colors.gemini, colors.grok, colors.perplexity],
                borderRadius: 8,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, max: 100, grid: { color: themeColors.grid }, ticks: { color: themeColors.text } },
                x: { grid: { display: false }, ticks: { color: themeColors.text } }
            }
        }
    });

    // Usage Trend Line Chart
    pageCharts.usage = new Chart(document.getElementById('usageTrendChart'), {
        type: 'line',
        data: {
            labels: ['Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс'],
            datasets: [
                { label: 'ChatGPT', data: [180, 195, 210, 185, 220, 150, 165], borderColor: colors.gpt, tension: 0.4, borderWidth: 2, pointRadius: 0 },
                { label: 'DeepSeek', data: [120, 135, 145, 130, 155, 95, 110], borderColor: colors.deepseek, tension: 0.4, borderWidth: 2, pointRadius: 0 },
                { label: 'Gemini', data: [100, 110, 125, 115, 130, 80, 95], borderColor: colors.gemini, tension: 0.4, borderWidth: 2, pointRadius: 0 },
                { label: 'Grok', data: [85, 95, 105, 90, 115, 70, 80], borderColor: colors.grok, tension: 0.4, borderWidth: 2, pointRadius: 0 },
                { label: 'Perplexity', data: [55, 65, 75, 60, 80, 45, 55], borderColor: colors.perplexity, tension: 0.4, borderWidth: 2, pointRadius: 0 },
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom', labels: { padding: 20, usePointStyle: true, pointStyle: 'circle' } } },
            scales: {
                y: { beginAtZero: true, grid: { color: themeColors.grid }, ticks: { color: themeColors.text } },
                x: { grid: { display: false }, ticks: { color: themeColors.text } }
            }
        }
    });

    // Quality Radar Chart
    pageCharts.quality = new Chart(document.getElementById('qualityRadarChart'), {
        type: 'radar',
        data: {
            labels: ['Релевантность', 'Точность', 'Полнота', 'Ясность', 'Нейтральность'],
            datasets: [
                { label: 'ChatGPT', data: [92, 88, 85, 90, 78], borderColor: colors.gpt, backgroundColor: colors.gpt + '20', borderWidth: 2 },
                { label: 'Gemini', data: [85, 90, 80, 82, 85], borderColor: colors.gemini, backgroundColor: colors.gemini + '20', borderWidth: 2 },
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom' } },
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
        }
    });

    // Tone Pie Chart
    pageCharts.tone = new Chart(document.getElementById('tonePieChart'), {
        type: 'doughnut',
        data: {
            labels: ['Позитивная', 'Нейтральная', 'Негативная'],
            datasets: [{ data: [68, 24, 8], backgroundColor: [colors.success, colors.primary, colors.danger], borderWidth: 0 }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, cutout: '65%' }
    });

    // Risk Pie Chart
    pageCharts.risk = new Chart(document.getElementById('riskPieChart'), {
        type: 'doughnut',
        data: {
            labels: ['Низкий', 'Средний', 'Высокий'],
            datasets: [{ data: [75, 18, 7], backgroundColor: [colors.success, colors.warning, colors.danger], borderWidth: 0 }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, cutout: '65%' }
    });
}

document.addEventListener('DOMContentLoaded', initLlmCharts);
</script>
SCRIPTS;
?>
