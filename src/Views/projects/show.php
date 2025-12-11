<?php
// Prepare metrics data with defaults
$promptQuality = $metrics['promptQuality'] ?? ['overall' => 0, 'specificity' => 0, 'completeness' => 0, 'neutrality' => 0, 'clarity' => 0, 'taskType' => 0];
$answerQuality = $metrics['answerQuality'] ?? ['overall' => 0, 'specificity' => 0, 'completeness' => 0, 'relevance' => 0, 'neutrality' => 0, 'clarity' => 0, 'accuracy' => 0];
$eeatMetrics = $metrics['eeat'] ?? ['overall' => 0, 'experience' => 0, 'expertise' => 0, 'authority' => 0, 'trust' => 0];

// Prepare source distribution with defaults
$geoDistribution = $sourceDistribution['geography'] ?? [];
$typeDistribution = $sourceDistribution['types'] ?? [];
$llmDist = $llmDistribution ?? [];

// Get top items for legends
$topGeo = array_slice($geoDistribution, 0, 3, true);
$topTypes = array_slice($typeDistribution, 0, 3, true);
$topLlm = array_slice($llmDist, 0, 3, true);
?>
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
            <div class="score-trend <?= ($project['trend_direction'] ?? 'up') === 'up' ? 'up' : 'down' ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="<?= ($project['trend_direction'] ?? 'up') === 'up' ? 'm18 15-6-6-6 6' : 'm6 9 6 6 6-6' ?>"/>
                </svg>
                <?= ($project['trend_direction'] ?? 'up') === 'up' ? '+' : '-' ?><?= abs($project['trend_percent'] ?? 0) ?>% за неделю
            </div>
        </div>
    </div>

    <div class="hero-stats">
        <div class="hero-stat">
            <div class="hero-stat-label">Обработано промтов</div>
            <div class="hero-stat-value"><?= number_format($stats['processedPrompts'] ?? 0) ?></div>
        </div>
        <div class="hero-stat">
            <div class="hero-stat-label">Уникальных источников</div>
            <div class="hero-stat-value"><?= number_format($stats['uniqueSources'] ?? 0) ?></div>
        </div>
        <div class="hero-stat">
            <div class="hero-stat-label">Средняя тональность</div>
            <div class="hero-stat-value"><?= $stats['avgTone'] ?? '+0.00' ?></div>
        </div>
        <div class="hero-stat">
            <div class="hero-stat-label">LLM моделей</div>
            <div class="hero-stat-value"><?= $stats['llmModels'] ?? 0 ?></div>
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
            <span class="analysis-badge <?= $promptQuality['overall'] >= 70 ? 'high' : ($promptQuality['overall'] >= 50 ? 'medium' : 'low') ?>"><?= number_format($promptQuality['overall'], 1) ?></span>
        </div>
        <div class="radar-container">
            <canvas id="promptsRadarChart"></canvas>
        </div>
        <div class="legend">
            <div class="legend-item">
                <span class="legend-dot" style="background: #6366f1;"></span>
                <span>Конкретность: <?= (int)$promptQuality['specificity'] ?>%</span>
            </div>
            <div class="legend-item">
                <span class="legend-dot" style="background: #6366f1;"></span>
                <span>Нейтральность: <?= (int)$promptQuality['neutrality'] ?>%</span>
            </div>
        </div>
    </div>

    <div class="analysis-card">
        <div class="analysis-header">
            <div>
                <div class="analysis-title">Качество ответов</div>
                <div class="analysis-subtitle">Комплексная оценка генерации</div>
            </div>
            <span class="analysis-badge <?= $answerQuality['overall'] >= 70 ? 'high' : ($answerQuality['overall'] >= 50 ? 'medium' : 'low') ?>"><?= number_format($answerQuality['overall'], 1) ?></span>
        </div>
        <div class="radar-container">
            <canvas id="answersRadarChart"></canvas>
        </div>
        <div class="legend">
            <div class="legend-item">
                <span class="legend-dot" style="background: #22c55e;"></span>
                <span>Соответствие: <?= (int)$answerQuality['relevance'] ?>%</span>
            </div>
            <div class="legend-item">
                <span class="legend-dot" style="background: #22c55e;"></span>
                <span>Полнота: <?= (int)$answerQuality['completeness'] ?>%</span>
            </div>
        </div>
    </div>

    <div class="analysis-card">
        <div class="analysis-header">
            <div>
                <div class="analysis-title">E-E-A-T оценка</div>
                <div class="analysis-subtitle">Качество источников</div>
            </div>
            <span class="analysis-badge <?= $eeatMetrics['overall'] >= 70 ? 'high' : ($eeatMetrics['overall'] >= 50 ? 'medium' : 'low') ?>"><?= number_format($eeatMetrics['overall'], 1) ?></span>
        </div>
        <div class="radar-container">
            <canvas id="eeatRadarChart"></canvas>
        </div>
        <div class="legend">
            <div class="legend-item">
                <span class="legend-dot" style="background: #8b5cf6;"></span>
                <span>Надёжность: <?= (int)$eeatMetrics['trust'] ?>%</span>
            </div>
            <div class="legend-item">
                <span class="legend-dot" style="background: #8b5cf6;"></span>
                <span>Экспертиза: <?= (int)$eeatMetrics['expertise'] ?>%</span>
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
            <?php
            $geoColors = ['#6366f1', '#22c55e', '#f59e0b', '#8b5cf6', '#ec4899'];
            $i = 0;
            foreach ($topGeo as $country => $percent):
            ?>
            <div class="legend-item">
                <span class="legend-dot" style="background: <?= $geoColors[$i % count($geoColors)] ?>;"></span>
                <span><?= $percent ?>% — <?= htmlspecialchars($country) ?></span>
            </div>
            <?php $i++; endforeach; ?>
            <?php if (empty($topGeo)): ?>
            <div class="legend-item">
                <span class="legend-dot" style="background: #6366f1;"></span>
                <span>Нет данных</span>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="source-card">
        <div class="source-title">Типология источников</div>
        <div class="source-subtitle">По характеру площадок</div>
        <div class="pie-container">
            <canvas id="typesPieChart"></canvas>
        </div>
        <div class="legend">
            <?php
            $typeColors = ['#6366f1', '#22c55e', '#f59e0b', '#8b5cf6', '#ec4899'];
            $i = 0;
            foreach ($topTypes as $type => $percent):
            ?>
            <div class="legend-item">
                <span class="legend-dot" style="background: <?= $typeColors[$i % count($typeColors)] ?>;"></span>
                <span><?= $percent ?>% — <?= htmlspecialchars($type) ?></span>
            </div>
            <?php $i++; endforeach; ?>
            <?php if (empty($topTypes)): ?>
            <div class="legend-item">
                <span class="legend-dot" style="background: #6366f1;"></span>
                <span>Нет данных</span>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="source-card">
        <div class="source-title">Распределение по LLM</div>
        <div class="source-subtitle">Использование моделями</div>
        <div class="pie-container">
            <canvas id="llmPieChart"></canvas>
        </div>
        <div class="legend">
            <?php
            $llmColors = ['#6366f1', '#22c55e', '#f59e0b', '#8b5cf6', '#ec4899'];
            $i = 0;
            foreach ($topLlm as $model => $percent):
            ?>
            <div class="legend-item">
                <span class="legend-dot" style="background: <?= $llmColors[$i % count($llmColors)] ?>;"></span>
                <span><?= $percent ?>% — <?= htmlspecialchars($model) ?></span>
            </div>
            <?php $i++; endforeach; ?>
            <?php if (empty($topLlm)): ?>
            <div class="legend-item">
                <span class="legend-dot" style="background: #6366f1;"></span>
                <span>Нет данных</span>
            </div>
            <?php endif; ?>
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
// Prepare chart data as JSON
$promptChartData = json_encode([
    (int)$promptQuality['specificity'],
    (int)$promptQuality['completeness'],
    (int)$promptQuality['neutrality'],
    (int)$promptQuality['clarity'],
    (int)$promptQuality['taskType']
]);

$answerChartData = json_encode([
    (int)$answerQuality['specificity'],
    (int)$answerQuality['completeness'],
    (int)$answerQuality['relevance'],
    (int)$answerQuality['neutrality'],
    (int)$answerQuality['clarity'],
    (int)$answerQuality['accuracy']
]);

$eeatChartData = json_encode([
    (int)$eeatMetrics['experience'],
    (int)$eeatMetrics['expertise'],
    (int)$eeatMetrics['authority'],
    (int)$eeatMetrics['trust']
]);

$geoLabels = json_encode(array_keys($geoDistribution) ?: ['Нет данных']);
$geoValues = json_encode(array_values($geoDistribution) ?: [100]);

$typeLabels = json_encode(array_keys($typeDistribution) ?: ['Нет данных']);
$typeValues = json_encode(array_values($typeDistribution) ?: [100]);

$llmLabels = json_encode(array_keys($llmDist) ?: ['Нет данных']);
$llmValues = json_encode(array_values($llmDist) ?: [100]);

$pageScripts = <<<SCRIPTS
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

    const colorPalette = [colors.primary, colors.success, colors.warning, colors.secondary, colors.pink, '#06b6d4', '#ef4444', '#84cc16'];

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

    // Prompts Quality Radar Chart
    pageCharts.prompts = new Chart(document.getElementById('promptsRadarChart'), {
        type: 'radar',
        data: {
            labels: ['Конкретность', 'Полнота', 'Нейтральность', 'Ясность', 'Тип задачи'],
            datasets: [{
                data: {$promptChartData},
                backgroundColor: 'rgba(99, 102, 241, 0.2)',
                borderColor: colors.primary,
                borderWidth: 2,
                pointBackgroundColor: colors.primary,
                pointRadius: 4
            }]
        },
        options: radarOptions
    });

    // Answers Quality Radar Chart
    pageCharts.answers = new Chart(document.getElementById('answersRadarChart'), {
        type: 'radar',
        data: {
            labels: ['Конкретность', 'Полнота', 'Соответствие', 'Нейтральность', 'Ясность', 'Точность'],
            datasets: [{
                data: {$answerChartData},
                backgroundColor: 'rgba(34, 197, 94, 0.2)',
                borderColor: colors.success,
                borderWidth: 2,
                pointBackgroundColor: colors.success,
                pointRadius: 4
            }]
        },
        options: radarOptions
    });

    // E-E-A-T Radar Chart
    pageCharts.eeat = new Chart(document.getElementById('eeatRadarChart'), {
        type: 'radar',
        data: {
            labels: ['Опыт', 'Экспертиза', 'Авторитетность', 'Надёжность'],
            datasets: [{
                data: {$eeatChartData},
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

    // Geography Distribution Pie Chart
    const geoLabels = {$geoLabels};
    const geoValues = {$geoValues};
    pageCharts.geo = new Chart(document.getElementById('geoPieChart'), {
        type: 'doughnut',
        data: {
            labels: geoLabels,
            datasets: [{
                data: geoValues,
                backgroundColor: colorPalette.slice(0, geoLabels.length),
                borderWidth: 0
            }]
        },
        options: pieOptions
    });

    // Types Distribution Pie Chart
    const typeLabels = {$typeLabels};
    const typeValues = {$typeValues};
    pageCharts.types = new Chart(document.getElementById('typesPieChart'), {
        type: 'doughnut',
        data: {
            labels: typeLabels,
            datasets: [{
                data: typeValues,
                backgroundColor: colorPalette.slice(0, typeLabels.length),
                borderWidth: 0
            }]
        },
        options: pieOptions
    });

    // LLM Distribution Pie Chart
    const llmLabels = {$llmLabels};
    const llmValues = {$llmValues};
    pageCharts.llm = new Chart(document.getElementById('llmPieChart'), {
        type: 'doughnut',
        data: {
            labels: llmLabels,
            datasets: [{
                data: llmValues,
                backgroundColor: colorPalette.slice(0, llmLabels.length),
                borderWidth: 0
            }]
        },
        options: pieOptions
    });
}

document.addEventListener('DOMContentLoaded', initProjectCharts);
</script>
SCRIPTS;
?>
