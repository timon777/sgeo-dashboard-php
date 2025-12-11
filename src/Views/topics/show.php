<!-- Topic Hero -->
<div class="topic-hero">
    <div class="hero-top">
        <div class="hero-info">
            <div class="topic-badge">
                <span>📋</span>
                Топик
            </div>
            <h1 class="topic-title"><?= htmlspecialchars($topic['topic'] ?? 'Без названия') ?></h1>
            <p class="topic-description"><?= htmlspecialchars($topic['prompt_pattern'] ?? '') ?></p>
        </div>
        <div class="hero-score">
            <div class="score-label">Средний балл</div>
            <div class="score-value"><?= (int)$stats['avgScore'] ?><span class="score-suffix">%</span></div>
            <div class="score-responses">
                <?= $stats['totalResponses'] ?> ответов
            </div>
        </div>
    </div>

    <div class="hero-stats">
        <div class="hero-stat">
            <div class="hero-stat-label">Всего ответов</div>
            <div class="hero-stat-value"><?= number_format($stats['totalResponses']) ?></div>
        </div>
        <div class="hero-stat">
            <div class="hero-stat-label">Позитивных</div>
            <div class="hero-stat-value sentiment-positive"><?= $stats['sentimentStats']['positive'] ?></div>
        </div>
        <div class="hero-stat">
            <div class="hero-stat-label">Нейтральных</div>
            <div class="hero-stat-value sentiment-neutral"><?= $stats['sentimentStats']['neutral'] ?></div>
        </div>
        <div class="hero-stat">
            <div class="hero-stat-label">Негативных</div>
            <div class="hero-stat-value sentiment-negative"><?= $stats['sentimentStats']['negative'] ?></div>
        </div>
    </div>
</div>

<!-- Tabs -->
<div class="tabs-container">
    <div class="tabs">
        <a href="?tab=overview" class="tab <?= $currentTab === 'overview' ? 'active' : '' ?>">Обзор</a>
        <a href="?tab=responses" class="tab <?= $currentTab === 'responses' ? 'active' : '' ?>">Ответы</a>
    </div>
</div>

<?php if ($currentTab === 'overview'): ?>
<!-- Overview Tab -->
<div class="tab-content">
    <div class="analysis-grid">
        <div class="analysis-card">
            <div class="analysis-header">
                <div>
                    <div class="analysis-title">Распределение тональности</div>
                    <div class="analysis-subtitle">По всем ответам</div>
                </div>
            </div>
            <div class="pie-container">
                <canvas id="sentimentPieChart"></canvas>
            </div>
            <div class="legend">
                <div class="legend-item">
                    <span class="legend-dot" style="background: #22c55e;"></span>
                    <span>Позитивная: <?= $stats['sentimentStats']['positive'] ?></span>
                </div>
                <div class="legend-item">
                    <span class="legend-dot" style="background: #6366f1;"></span>
                    <span>Нейтральная: <?= $stats['sentimentStats']['neutral'] ?></span>
                </div>
                <div class="legend-item">
                    <span class="legend-dot" style="background: #ef4444;"></span>
                    <span>Негативная: <?= $stats['sentimentStats']['negative'] ?></span>
                </div>
            </div>
        </div>

        <div class="analysis-card">
            <div class="analysis-header">
                <div>
                    <div class="analysis-title">Информация о топике</div>
                    <div class="analysis-subtitle">Основные данные</div>
                </div>
            </div>
            <div class="info-list">
                <div class="info-item">
                    <span class="info-label">Язык:</span>
                    <span class="info-value"><?= htmlspecialchars($topic['language'] ?? 'N/A') ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Статус:</span>
                    <span class="info-value"><?= ($topic['is_active'] ?? false) ? 'Активен' : 'Неактивен' ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Создан:</span>
                    <span class="info-value"><?= isset($topic['created_at']) ? date('d.m.Y H:i', strtotime($topic['created_at'])) : 'N/A' ?></span>
                </div>
            </div>
        </div>
    </div>

    <?php if (!empty($topic['reference_answer'])): ?>
    <div class="reference-section">
        <h3 class="section-subtitle">Эталонный ответ</h3>
        <div class="reference-card">
            <p><?= nl2br(htmlspecialchars($topic['reference_answer'])) ?></p>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php elseif ($currentTab === 'responses'): ?>
<!-- Responses Tab -->
<div class="tab-content">
    <!-- Filters -->
    <div class="filters-row">
        <select class="select-field" style="width: auto; min-width: 150px;" id="sentimentFilter">
            <option value="">Все тональности</option>
            <option value="positive">Позитивная</option>
            <option value="neutral">Нейтральная</option>
            <option value="negative">Негативная</option>
        </select>
    </div>

    <!-- Responses Table -->
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Модель</th>
                    <th>Ответ</th>
                    <th class="sortable">Тональность</th>
                    <th class="sortable">Coherence</th>
                    <th class="sortable">Consistency</th>
                    <th class="sortable">Fluency</th>
                    <th class="sortable">Relevance</th>
                    <th class="sortable">Avg Score</th>
                    <th>Дата</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($evaluations)): ?>
                <tr>
                    <td colspan="9" style="text-align: center; padding: 40px; color: var(--text-tertiary);">
                        Нет данных для отображения
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($evaluations as $eval): ?>
                <?php
                    $aiResponse = $responses[$eval['ai_response_id']] ?? null;
                    $sentiment = strtolower($eval['sentiment'] ?? 'neutral');
                    $sentimentLabels = [
                        'positive' => 'Позитивная',
                        'neutral' => 'Нейтральная',
                        'negative' => 'Негативная'
                    ];
                    $avgScore = (int)($eval['avg_score'] ?? 0);
                ?>
                <tr class="response-row" data-sentiment="<?= htmlspecialchars($sentiment) ?>">
                    <td>
                        <span class="llm-badge"><?= htmlspecialchars($eval['evaluator_model'] ?? 'N/A') ?></span>
                    </td>
                    <td>
                        <div class="response-preview" title="<?= htmlspecialchars($aiResponse['response'] ?? $eval['reasoning'] ?? '') ?>">
                            <?= htmlspecialchars(mb_substr($aiResponse['response'] ?? $eval['reasoning'] ?? '', 0, 100)) ?>...
                        </div>
                    </td>
                    <td>
                        <span class="tone-badge <?= $sentiment ?>">
                            <?= $sentimentLabels[$sentiment] ?? ucfirst($sentiment) ?>
                        </span>
                    </td>
                    <td>
                        <span class="metric-value"><?= (int)($eval['coherence'] ?? 0) ?></span>
                    </td>
                    <td>
                        <span class="metric-value"><?= (int)($eval['consistency'] ?? 0) ?></span>
                    </td>
                    <td>
                        <span class="metric-value"><?= (int)($eval['fluency'] ?? 0) ?></span>
                    </td>
                    <td>
                        <span class="metric-value"><?= (int)($eval['relevance'] ?? 0) ?></span>
                    </td>
                    <td>
                        <span class="score-badge <?= $avgScore >= 80 ? 'high' : ($avgScore >= 60 ? 'medium' : 'low') ?>">
                            <?= $avgScore ?>
                        </span>
                    </td>
                    <td style="color: var(--text-tertiary); font-size: 13px; white-space: nowrap;">
                        <?= isset($eval['evaluated_at']) ? date('d.m.Y H:i', strtotime($eval['evaluated_at'])) : 'N/A' ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div style="text-align: center; margin-top: 16px; color: var(--text-tertiary); font-size: 13px;">
        Всего записей: <?= count($evaluations) ?>
    </div>
</div>
<?php endif; ?>

<style>
/* Topic Hero */
.topic-hero {
    background: var(--bg-card);
    border: 1px solid var(--border-subtle);
    border-radius: var(--radius-xl);
    padding: 32px;
    margin-bottom: 24px;
    position: relative;
    overflow: hidden;
}

.topic-hero::before {
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

.topic-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    margin-bottom: 16px;
    background: rgba(99, 102, 241, 0.12);
    color: var(--accent-primary);
}

.topic-title {
    font-size: 28px;
    font-weight: 700;
    letter-spacing: -0.5px;
    margin-bottom: 12px;
    background: linear-gradient(135deg, var(--text-primary) 0%, var(--text-secondary) 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.topic-description {
    font-size: 15px;
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
    font-size: 56px;
    font-weight: 700;
    font-family: 'JetBrains Mono', monospace;
    background: var(--accent-gradient);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    line-height: 1;
}

.score-suffix {
    font-size: 20px;
}

.score-responses {
    margin-top: 8px;
    font-size: 13px;
    color: var(--text-tertiary);
}

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

.hero-stat-value.sentiment-positive { color: var(--success); }
.hero-stat-value.sentiment-neutral { color: var(--accent-primary); }
.hero-stat-value.sentiment-negative { color: var(--danger); }

/* Tabs */
.tabs-container {
    margin-bottom: 24px;
}

.tabs {
    display: flex;
    gap: 4px;
    background: var(--bg-card);
    padding: 4px;
    border-radius: var(--radius-md);
    border: 1px solid var(--border-subtle);
    width: fit-content;
}

.tab {
    padding: 10px 20px;
    border-radius: var(--radius-sm);
    font-size: 14px;
    font-weight: 500;
    color: var(--text-tertiary);
    text-decoration: none;
    transition: all var(--transition-base);
}

.tab:hover {
    color: var(--text-primary);
    background: rgba(255, 255, 255, 0.05);
}

.tab.active {
    color: var(--text-primary);
    background: var(--accent-primary);
}

/* Tab Content */
.tab-content {
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Tone Badge */
.tone-badge {
    display: inline-flex;
    align-items: center;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
}

.tone-badge.positive {
    background: var(--success-bg);
    color: var(--success);
}

.tone-badge.neutral {
    background: rgba(99, 102, 241, 0.12);
    color: var(--accent-primary);
}

.tone-badge.negative {
    background: var(--danger-bg);
    color: var(--danger);
}

/* Score Badge */
.score-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 36px;
    padding: 4px 8px;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 600;
    font-family: 'JetBrains Mono', monospace;
}

.score-badge.high {
    background: var(--success-bg);
    color: var(--success);
}

.score-badge.medium {
    background: var(--warning-bg);
    color: var(--warning);
}

.score-badge.low {
    background: var(--danger-bg);
    color: var(--danger);
}

/* Metric Value */
.metric-value {
    font-family: 'JetBrains Mono', monospace;
    font-weight: 500;
    font-size: 13px;
}

/* Response Preview */
.response-preview {
    max-width: 250px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    color: var(--text-tertiary);
    font-size: 13px;
}

/* LLM Badge */
.llm-badge {
    display: inline-flex;
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 600;
    background: rgba(99, 102, 241, 0.12);
    color: var(--accent-primary);
}

/* Info List */
.info-list {
    display: flex;
    flex-direction: column;
    gap: 16px;
    padding: 16px 0;
}

.info-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.info-label {
    color: var(--text-tertiary);
    font-size: 13px;
}

.info-value {
    color: var(--text-primary);
    font-weight: 500;
    font-size: 14px;
}

/* Reference Section */
.reference-section {
    margin-top: 32px;
}

.section-subtitle {
    font-size: 16px;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 16px;
}

.reference-card {
    background: var(--bg-card);
    border: 1px solid var(--border-subtle);
    border-radius: var(--radius-lg);
    padding: 24px;
}

.reference-card p {
    color: var(--text-secondary);
    line-height: 1.7;
    font-size: 14px;
}

/* Filters */
.filters-row {
    display: flex;
    gap: 16px;
    margin-bottom: 24px;
    flex-wrap: wrap;
}

/* Pie Container */
.pie-container {
    height: 200px;
    margin: 20px 0;
}

/* Analysis Grid */
.analysis-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 24px;
    margin-bottom: 32px;
}

.analysis-card {
    background: var(--bg-card);
    border: 1px solid var(--border-subtle);
    border-radius: var(--radius-lg);
    padding: 24px;
}

.analysis-header {
    margin-bottom: 16px;
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

/* Legend */
.legend {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.legend-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    color: var(--text-secondary);
}

.legend-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
}

/* Responsive */
@media (max-width: 1200px) {
    .analysis-grid {
        grid-template-columns: 1fr;
    }
    .hero-stats {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .hero-top {
        flex-direction: column;
    }
    .hero-stats {
        grid-template-columns: 1fr;
    }
    .tabs {
        width: 100%;
    }
    .tab {
        flex: 1;
        text-align: center;
    }
}
</style>

<?php
$pageScripts = <<<SCRIPTS
<script>
function initTopicCharts() {
    const themeColors = getChartColors();
    updateChartDefaults();

    const sentimentData = {
        positive: {$stats['sentimentStats']['positive']},
        neutral: {$stats['sentimentStats']['neutral']},
        negative: {$stats['sentimentStats']['negative']}
    };

    const sentimentCtx = document.getElementById('sentimentPieChart');
    if (sentimentCtx) {
        pageCharts.sentiment = new Chart(sentimentCtx, {
            type: 'doughnut',
            data: {
                labels: ['Позитивная', 'Нейтральная', 'Негативная'],
                datasets: [{
                    data: [sentimentData.positive, sentimentData.neutral, sentimentData.negative],
                    backgroundColor: ['#22c55e', '#6366f1', '#ef4444'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                cutout: '65%'
            }
        });
    }

    // Sentiment filter
    const sentimentFilter = document.getElementById('sentimentFilter');
    if (sentimentFilter) {
        sentimentFilter.addEventListener('change', function() {
            const value = this.value;
            const rows = document.querySelectorAll('.response-row');
            rows.forEach(row => {
                if (!value || row.dataset.sentiment === value) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }
}

document.addEventListener('DOMContentLoaded', initTopicCharts);
</script>
SCRIPTS;
?>
