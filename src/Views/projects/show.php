<!-- Project Hero -->
<div class="project-hero">
    <div class="hero-top">
        <div class="hero-info">
            <div class="project-badge <?= $project['type'] ?>">
                <span><?= $project['icon'] ?></span>
                <?= htmlspecialchars($project['badge']) ?>
            </div>
            <h1 class="project-title"><?= htmlspecialchars($project['name']) ?></h1>
            <p class="project-description"><?= htmlspecialchars($project['description']) ?></p>
        </div>
        <div class="hero-score">
            <div class="score-label">Общий индекс точности</div>
            <div class="score-value"><?= (int)$project['accuracy_score'] ?><span class="score-suffix">%</span></div>
            <div class="score-trend <?= $project['trend_direction'] === 'up' ? 'up' : 'down' ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="<?= $project['trend_direction'] === 'up' ? 'm18 15-6-6-6 6' : 'm6 9 6 6 6-6' ?>"/>
                </svg>
                <?= $project['trend_direction'] === 'up' ? '+' : '-' ?><?= abs($project['trend_percent']) ?>% за неделю
            </div>
        </div>
    </div>

    <div class="hero-stats">
        <div class="hero-stat">
            <div class="hero-stat-label">Обработано промтов</div>
            <div class="hero-stat-value"><?= number_format($stats['processedPrompts']) ?></div>
        </div>
        <div class="hero-stat">
            <div class="hero-stat-label">Уникальных источников</div>
            <div class="hero-stat-value"><?= number_format($stats['uniqueSources']) ?></div>
        </div>
        <div class="hero-stat">
            <div class="hero-stat-label">Средняя тональность</div>
            <div class="hero-stat-value"><?= $stats['avgTone'] ?></div>
        </div>
        <div class="hero-stat">
            <div class="hero-stat-label">LLM моделей</div>
            <div class="hero-stat-value"><?= $stats['llmModels'] ?></div>
        </div>
    </div>
</div>

<!-- Analysis Cards -->
<div class="analysis-grid">
    <div class="analysis-card">
        <div class="analysis-header">
            <div>
                <div class="analysis-title">Качество промтов</div>
                <div class="analysis-subtitle">Оценка формулировки запросов</div>
            </div>
            <span class="analysis-badge high">83.6</span>
        </div>
        <div class="radar-container">
            <canvas id="promptsRadarChart"></canvas>
        </div>
        <div class="legend">
            <div class="legend-item">
                <span class="legend-dot" style="background: #6366f1;"></span>
                <span>Конкретность: 85%</span>
            </div>
            <div class="legend-item">
                <span class="legend-dot" style="background: #6366f1;"></span>
                <span>Нейтральность: 92%</span>
            </div>
        </div>
    </div>

    <div class="analysis-card">
        <div class="analysis-header">
            <div>
                <div class="analysis-title">Качество ответов</div>
                <div class="analysis-subtitle">Комплексная оценка генерации</div>
            </div>
            <span class="analysis-badge high">85.2</span>
        </div>
        <div class="radar-container">
            <canvas id="answersRadarChart"></canvas>
        </div>
        <div class="legend">
            <div class="legend-item">
                <span class="legend-dot" style="background: #22c55e;"></span>
                <span>Соответствие: 91%</span>
            </div>
            <div class="legend-item">
                <span class="legend-dot" style="background: #22c55e;"></span>
                <span>Полнота: 88%</span>
            </div>
        </div>
    </div>

    <div class="analysis-card">
        <div class="analysis-header">
            <div>
                <div class="analysis-title">E-E-A-T оценка</div>
                <div class="analysis-subtitle">Качество источников</div>
            </div>
            <span class="analysis-badge high">83.8</span>
        </div>
        <div class="radar-container">
            <canvas id="eeatRadarChart"></canvas>
        </div>
        <div class="legend">
            <div class="legend-item">
                <span class="legend-dot" style="background: #8b5cf6;"></span>
                <span>Надёжность: 90%</span>
            </div>
            <div class="legend-item">
                <span class="legend-dot" style="background: #8b5cf6;"></span>
                <span>Экспертиза: 88%</span>
            </div>
        </div>
    </div>
</div>

<!-- Narratives Section -->
<h2 class="section-title">Ключевые нарративы проекта</h2>
<div class="accordion">
    <?php foreach ($narratives as $index => $narrative): ?>
    <div class="accordion-item <?= $index === 0 ? 'active' : '' ?>">
        <div class="accordion-header" onclick="toggleAccordion(this)">
            <span class="accordion-title"><?= htmlspecialchars($narrative['title']) ?></span>
            <svg class="accordion-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="m6 9 6 6 6-6"/>
            </svg>
        </div>
        <div class="accordion-content">
            <div class="accordion-body"><?= htmlspecialchars($narrative['description']) ?></div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Sources Section -->
<h2 class="section-title">Распределение источников</h2>
<div class="sources-section">
    <div class="source-card">
        <div class="source-title">География источников</div>
        <div class="source-subtitle">Распределение по странам</div>
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
                <span>15% — Западные</span>
            </div>
        </div>
    </div>

    <div class="source-card">
        <div class="source-title">Типология источников</div>
        <div class="source-subtitle">По характеру площадок</div>
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
                <span>25% — Гос. сайты</span>
            </div>
            <div class="legend-item">
                <span class="legend-dot" style="background: #f59e0b;"></span>
                <span>20% — Соцсети</span>
            </div>
        </div>
    </div>

    <div class="source-card">
        <div class="source-title">Распределение по LLM</div>
        <div class="source-subtitle">Использование моделями</div>
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
        </div>
    </div>
</div>

<style>
/* Project Hero */
.project-hero {
    background: var(--bg-card);
    border: 1px solid var(--border-subtle);
    border-radius: var(--radius-xl);
    padding: 32px;
    margin-bottom: 32px;
    position: relative;
    overflow: hidden;
}

.project-hero::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: var(--accent-gradient);
}

.hero-top {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 32px;
    margin-bottom: 32px;
}

.hero-info {
    flex: 1;
}

.project-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    margin-bottom: 16px;
}

.project-badge.gov {
    background: rgba(99, 102, 241, 0.12);
    color: var(--accent-primary);
}

.project-badge.private {
    background: var(--success-bg);
    color: var(--success);
}

.project-title {
    font-size: 36px;
    font-weight: 700;
    letter-spacing: -0.5px;
    margin-bottom: 12px;
    background: linear-gradient(135deg, var(--text-primary) 0%, var(--text-secondary) 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.project-description {
    font-size: 16px;
    color: var(--text-tertiary);
    line-height: 1.7;
    max-width: 700px;
}

.hero-score {
    text-align: center;
    padding: 24px 40px;
    background: rgba(255, 255, 255, 0.03);
    border-radius: var(--radius-lg);
    border: 1px solid var(--border-subtle);
}

.score-label {
    font-size: 13px;
    color: var(--text-tertiary);
    margin-bottom: 8px;
}

.score-value {
    font-size: 72px;
    font-weight: 700;
    font-family: 'JetBrains Mono', monospace;
    background: var(--accent-gradient);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    line-height: 1;
}

.score-suffix {
    font-size: 24px;
}

.score-trend {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    margin-top: 8px;
    font-size: 14px;
    font-weight: 600;
}

.score-trend.up { color: var(--success); }
.score-trend.down { color: var(--danger); }
.score-trend svg { width: 16px; height: 16px; }

.hero-stats {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
}

.hero-stat {
    padding: 20px;
    background: rgba(255, 255, 255, 0.03);
    border-radius: var(--radius-md);
    border: 1px solid var(--border-subtle);
}

.hero-stat-label {
    font-size: 12px;
    color: var(--text-tertiary);
    margin-bottom: 8px;
}

.hero-stat-value {
    font-size: 24px;
    font-weight: 700;
    font-family: 'JetBrains Mono', monospace;
    color: var(--text-primary);
}

/* Analysis Cards */
.analysis-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 24px;
    margin-bottom: 32px;
}

.analysis-card {
    background: var(--bg-card);
    border: 1px solid var(--border-subtle);
    border-radius: var(--radius-lg);
    padding: 24px;
    transition: all var(--transition-base);
}

.analysis-card:hover {
    border-color: var(--border-medium);
}

.analysis-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    margin-bottom: 20px;
}

.analysis-title {
    font-size: 15px;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 4px;
}

.analysis-subtitle {
    font-size: 12px;
    color: var(--text-tertiary);
}

.analysis-badge {
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 600;
    font-family: 'JetBrains Mono', monospace;
}

.analysis-badge.high {
    background: var(--success-bg);
    color: var(--success);
}

.radar-container {
    height: 200px;
    margin-bottom: 16px;
}

/* Sources Section */
.sources-section {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 24px;
}

@media (max-width: 1200px) {
    .analysis-grid, .sources-section {
        grid-template-columns: repeat(2, 1fr);
    }
    .hero-stats {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .analysis-grid, .sources-section, .hero-stats {
        grid-template-columns: 1fr;
    }
    .hero-top { flex-direction: column; }
}
</style>

<?php
$pageScripts = <<<'SCRIPTS'
<script>
function initProjectCharts() {
    const themeColors = getChartColors();
    updateChartDefaults();

    const colors = {
        primary: '#6366f1',
        secondary: '#8b5cf6',
        success: '#22c55e',
        warning: '#f59e0b',
        pink: '#ec4899'
    };

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

    pageCharts.prompts = new Chart(document.getElementById('promptsRadarChart'), {
        type: 'radar',
        data: {
            labels: ['Конкретность', 'Полнота', 'Нейтральность', 'Однозначность', 'Тип задачи'],
            datasets: [{
                data: [85, 78, 92, 88, 75],
                backgroundColor: 'rgba(99, 102, 241, 0.2)',
                borderColor: colors.primary,
                borderWidth: 2,
                pointBackgroundColor: colors.primary,
                pointRadius: 4
            }]
        },
        options: radarOptions
    });

    pageCharts.answers = new Chart(document.getElementById('answersRadarChart'), {
        type: 'radar',
        data: {
            labels: ['Конкретность', 'Полнота', 'Соответствие', 'Нейтральность', 'Ясность', 'Точность'],
            datasets: [{
                data: [82, 88, 91, 85, 79, 86],
                backgroundColor: 'rgba(34, 197, 94, 0.2)',
                borderColor: colors.success,
                borderWidth: 2,
                pointBackgroundColor: colors.success,
                pointRadius: 4
            }]
        },
        options: radarOptions
    });

    pageCharts.eeat = new Chart(document.getElementById('eeatRadarChart'), {
        type: 'radar',
        data: {
            labels: ['Опыт', 'Экспертиза', 'Авторитетность', 'Надёжность'],
            datasets: [{
                data: [75, 88, 82, 90],
                backgroundColor: 'rgba(139, 92, 246, 0.2)',
                borderColor: colors.secondary,
                borderWidth: 2,
                pointBackgroundColor: colors.secondary,
                pointRadius: 4
            }]
        },
        options: radarOptions
    });

    const pieOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        cutout: '65%'
    };

    pageCharts.geo = new Chart(document.getElementById('geoPieChart'), {
        type: 'doughnut',
        data: {
            labels: ['Казахстанские', 'Российские', 'Западные'],
            datasets: [{ data: [55, 30, 15], backgroundColor: [colors.primary, colors.success, colors.warning], borderWidth: 0 }]
        },
        options: pieOptions
    });

    pageCharts.types = new Chart(document.getElementById('typesPieChart'), {
        type: 'doughnut',
        data: {
            labels: ['СМИ', 'Гос. сайты', 'Соцсети', 'Блоги', 'Научные'],
            datasets: [{ data: [40, 25, 20, 10, 5], backgroundColor: [colors.primary, colors.success, colors.warning, colors.secondary, colors.pink], borderWidth: 0 }]
        },
        options: pieOptions
    });

    pageCharts.llm = new Chart(document.getElementById('llmPieChart'), {
        type: 'doughnut',
        data: {
            labels: ['GPT', 'Gemini', 'Claude', 'Perplexity', 'Прочие'],
            datasets: [{ data: [35, 25, 20, 15, 5], backgroundColor: [colors.primary, colors.success, colors.warning, colors.secondary, colors.pink], borderWidth: 0 }]
        },
        options: pieOptions
    });
}

document.addEventListener('DOMContentLoaded', initProjectCharts);
</script>
SCRIPTS;
?>
