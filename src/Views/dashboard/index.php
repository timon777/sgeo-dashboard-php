<div class="page-header animate-on-scroll">
    <div class="page-header-top">
        <div class="page-title-group">
            <h1 class="page-title">Аналитический дашборд</h1>
            <p class="page-subtitle">Мониторинг качества ответов LLM, анализ нарративов и оценка источников информации</p>
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
                    <line x1="12" y1="5" x2="12" y2="19"/>
                    <line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                Новый проект
            </button>
        </div>
    </div>

    <div class="tabs-container">
        <button class="tab active">Обзор</button>
        <button class="tab">По проектам</button>
        <button class="tab">По LLM</button>
        <button class="tab">По источникам</button>
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
            <div class="stat-trend up">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="m18 15-6-6-6 6"/>
                </svg>
                +12%
            </div>
        </div>
        <div class="stat-content">
            <div class="stat-label">Активных проектов</div>
            <div class="stat-value"><?= $stats['activeProjects'] ?></div>
        </div>
        <div class="stat-description">5 государственных, 2 частных</div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon-container green">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                </svg>
            </div>
            <div class="stat-trend up">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="m18 15-6-6-6 6"/>
                </svg>
                +8%
            </div>
        </div>
        <div class="stat-content">
            <div class="stat-label">Обработано промтов</div>
            <div class="stat-value"><?= number_format($stats['processedPrompts']) ?></div>
        </div>
        <div class="stat-description">За последние 30 дней</div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon-container purple">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                </svg>
            </div>
            <div class="stat-trend up">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="m18 15-6-6-6 6"/>
                </svg>
                +15%
            </div>
        </div>
        <div class="stat-content">
            <div class="stat-label">Проанализировано источников</div>
            <div class="stat-value"><?= number_format($stats['analyzedSources']) ?></div>
        </div>
        <div class="stat-description">Уникальных доменов</div>
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
            <div class="stat-trend down">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="m6 9 6 6 6-6"/>
                </svg>
                -3%
            </div>
        </div>
        <div class="stat-content">
            <div class="stat-label">Средняя точность</div>
            <div class="stat-value"><?= $stats['averageAccuracy'] ?>%</div>
        </div>
        <div class="stat-description">По всем LLM моделям</div>
    </div>
</div>

<!-- Charts Section -->
<div class="charts-section">
    <div class="chart-card">
        <div class="chart-header">
            <div>
                <div class="chart-title">Топ проектов по упоминаниям</div>
                <div class="chart-description">Количество обработанных запросов по каждому проекту</div>
            </div>
            <div class="chart-actions">
                <button class="chart-action-btn">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                        <polyline points="7,10 12,15 17,10"/>
                        <line x1="12" y1="15" x2="12" y2="3"/>
                    </svg>
                </button>
                <button class="chart-action-btn">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="1"/>
                        <circle cx="19" cy="12" r="1"/>
                        <circle cx="5" cy="12" r="1"/>
                    </svg>
                </button>
            </div>
        </div>
        <div class="chart-container">
            <canvas id="sourcesBarChart"></canvas>
        </div>
    </div>

    <div class="chart-card">
        <div class="chart-header">
            <div>
                <div class="chart-title">Производительность LLM</div>
                <div class="chart-description">Средний балл качества ответов по моделям</div>
            </div>
        </div>
        <div class="chart-container">
            <canvas id="llmPerformanceChart"></canvas>
        </div>
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
                <span>Средний балл: 83.6</span>
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
                <span>Средний балл: 85.2</span>
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
                <span>Средний балл: 83.8</span>
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
            <div class="legend-item">
                <span class="legend-dot" style="background: #6366f1;"></span>
                <span>55% — Казахстанские</span>
            </div>
            <div class="legend-item">
                <span class="legend-dot" style="background: #22c55e;"></span>
                <span>30% — Российские</span>
            </div>
            <div class="legend-item">
                <span class="legend-dot" style="background: #f59e0b;"></span>
                <span>15% — Американские</span>
            </div>
        </div>
    </div>

    <div class="source-card">
        <div class="source-title">Типология источников</div>
        <div class="source-subtitle">Распределение по характеру площадок</div>
        <div class="pie-container">
            <canvas id="typesPieChart"></canvas>
        </div>
        <div class="legend">
            <div class="legend-item">
                <span class="legend-dot" style="background: #6366f1;"></span>
                <span>40% — СМИ</span>
            </div>
            <div class="legend-item">
                <span class="legend-dot" style="background: #22c55e;"></span>
                <span>25% — Государственные сайты</span>
            </div>
            <div class="legend-item">
                <span class="legend-dot" style="background: #f59e0b;"></span>
                <span>20% — Социальные сети</span>
            </div>
            <div class="legend-item">
                <span class="legend-dot" style="background: #8b5cf6;"></span>
                <span>10% — Блоги</span>
            </div>
            <div class="legend-item">
                <span class="legend-dot" style="background: #ec4899;"></span>
                <span>5% — Научные ресурсы</span>
            </div>
        </div>
    </div>

    <div class="source-card">
        <div class="source-title">Распределение по LLM</div>
        <div class="source-subtitle">Использование источников моделями</div>
        <div class="pie-container">
            <canvas id="llmPieChart"></canvas>
        </div>
        <div class="legend">
            <div class="legend-item">
                <span class="legend-dot" style="background: #6366f1;"></span>
                <span>35% — GPT</span>
            </div>
            <div class="legend-item">
                <span class="legend-dot" style="background: #22c55e;"></span>
                <span>25% — Gemini</span>
            </div>
            <div class="legend-item">
                <span class="legend-dot" style="background: #f59e0b;"></span>
                <span>20% — Claude</span>
            </div>
            <div class="legend-item">
                <span class="legend-dot" style="background: #8b5cf6;"></span>
                <span>15% — Perplexity</span>
            </div>
            <div class="legend-item">
                <span class="legend-dot" style="background: #ec4899;"></span>
                <span>5% — Прочие</span>
            </div>
        </div>
    </div>
</div>

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

    // Bar Chart - Top Projects
    new Chart(document.getElementById('sourcesBarChart'), {
        type: 'bar',
        data: {
            labels: ['Имидж Президента', 'Январь 2022', 'Закон и порядок', 'Цифровой Казахстан', 'АЭС', 'Freedom Broker'],
            datasets: [{
                label: 'Количество упоминаний',
                data: [160, 145, 120, 95, 85, 65],
                backgroundColor: colors.primary,
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
                x: { grid: { display: false }, ticks: { maxRotation: 45, minRotation: 45, color: themeColors.text } }
            }
        }
    });

    // Horizontal Bar Chart - LLM Performance
    new Chart(document.getElementById('llmPerformanceChart'), {
        type: 'bar',
        data: {
            labels: ['ChatGPT', 'DeepSeek', 'Copilot', 'Perplexity', 'Gemini'],
            datasets: [{
                data: [4.8, 4.2, 3.9, 3.6, 3.2],
                backgroundColor: [colors.primary, colors.success, colors.warning, colors.secondary, colors.pink],
                borderRadius: 8,
                borderSkipped: false,
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { beginAtZero: true, max: 5, grid: { color: themeColors.grid }, ticks: { color: themeColors.text } },
                y: { grid: { display: false }, ticks: { color: themeColors.text } }
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

    new Chart(document.getElementById('promptsRadarChart'), {
        type: 'radar',
        data: {
            labels: ['Конкретность', 'Полнота', 'Нейтральность', 'Однозначность', 'Тип задачи'],
            datasets: [{
                data: [85, 78, 92, 88, 75],
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

    new Chart(document.getElementById('answersRadarChart'), {
        type: 'radar',
        data: {
            labels: ['Конкретность', 'Полнота', 'Соответствие', 'Нейтральность', 'Ясность', 'Точность'],
            datasets: [{
                data: [82, 88, 91, 85, 79, 86],
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

    new Chart(document.getElementById('eeatRadarChart'), {
        type: 'radar',
        data: {
            labels: ['Опыт', 'Экспертиза', 'Авторитетность', 'Надёжность'],
            datasets: [{
                data: [75, 88, 82, 90],
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

    new Chart(document.getElementById('geoPieChart'), {
        type: 'doughnut',
        data: {
            labels: ['Казахстанские', 'Российские', 'Американские'],
            datasets: [{ data: [55, 30, 15], backgroundColor: [colors.primary, colors.success, colors.warning], borderWidth: 0 }]
        },
        options: pieOptions
    });

    new Chart(document.getElementById('typesPieChart'), {
        type: 'doughnut',
        data: {
            labels: ['СМИ', 'Государственные', 'Соцсети', 'Блоги', 'Научные'],
            datasets: [{ data: [40, 25, 20, 10, 5], backgroundColor: [colors.primary, colors.success, colors.warning, colors.secondary, colors.pink], borderWidth: 0 }]
        },
        options: pieOptions
    });

    new Chart(document.getElementById('llmPieChart'), {
        type: 'doughnut',
        data: {
            labels: ['ChatGPT', 'DeepSeek', 'Grok', 'Gemini', 'Perplexity'],
            datasets: [{ data: [35, 25, 20, 15, 5], backgroundColor: [colors.primary, colors.success, colors.warning, colors.secondary, colors.pink], borderWidth: 0 }]
        },
        options: pieOptions
    });
}

document.addEventListener('DOMContentLoaded', initDashboardCharts);
</script>
SCRIPTS;
?>
