<div class="page-header animate-on-scroll">
    <div class="page-header-top">
        <div class="page-title-group">
            <h1 class="page-title">Ответы LLM</h1>
            <p class="page-subtitle">Все ответы моделей с оценками G-EVAL</p>
        </div>
        <div class="page-actions">
            <a href="/export/responses" class="btn btn-secondary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                    <polyline points="7,10 12,15 17,10"/>
                    <line x1="12" y1="15" x2="12" y2="3"/>
                </svg>
                Экспорт CSV
            </a>
        </div>
    </div>
</div>

<!-- Model Comparison -->
<?php if (!empty($modelComparison)): ?>
<div class="section-block">
    <h3 class="section-title">Сравнение моделей</h3>
    <div class="model-comparison">
        <?php foreach ($modelComparison as $model): ?>
        <div class="model-card">
            <div class="model-name"><?= htmlspecialchars($model['name']) ?></div>
            <div class="model-score"><?= $model['avgScore'] ?></div>
            <div class="model-meta"><?= $model['count'] ?> ответов</div>
            <div class="model-bar">
                <div class="model-bar-fill" style="width: <?= $model['avgScore'] ?>%"></div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<!-- Responses List -->
<div class="section-block">
    <h3 class="section-title">Список ответов <span class="count-badge"><?= $totalCount ?></span></h3>
    <div class="responses-list" id="responsesList">
        <?php foreach ($responses as $response): ?>
        <div class="response-card">
            <div class="response-header">
                <span class="llm-badge <?= strtolower($response['model']) ?>"><?= strtoupper($response['model']) ?></span>
                <a href="/topics/<?= htmlspecialchars($response['projectId']) ?>" class="project-link"><?= htmlspecialchars($response['projectName']) ?></a>
                <span class="response-date"><?= $response['date'] ?></span>
                <span class="tone-badge <?= $response['tone'] ?>">
                    <?php
                    $toneLabels = ['positive' => 'Позитивный', 'neutral' => 'Нейтральный', 'negative' => 'Негативный'];
                    echo $toneLabels[$response['tone']] ?? $response['tone'];
                    ?>
                </span>
            </div>
            <div class="response-prompt">
                <strong>Промт:</strong> <?= htmlspecialchars($response['prompt']) ?>
            </div>
            <div class="response-text"><?= htmlspecialchars($response['response']) ?></div>
            <div class="response-metrics">
                <div class="metric-mini">
                    <span class="metric-label">Связн.</span>
                    <span class="metric-value <?= $response['coherence'] >= 80 ? 'high' : ($response['coherence'] >= 60 ? 'medium' : 'low') ?>"><?= $response['coherence'] ?>%</span>
                </div>
                <div class="metric-mini">
                    <span class="metric-label">Согласов.</span>
                    <span class="metric-value <?= $response['consistency'] >= 80 ? 'high' : ($response['consistency'] >= 60 ? 'medium' : 'low') ?>"><?= $response['consistency'] ?>%</span>
                </div>
                <div class="metric-mini">
                    <span class="metric-label">Беглость</span>
                    <span class="metric-value <?= $response['fluency'] >= 80 ? 'high' : ($response['fluency'] >= 60 ? 'medium' : 'low') ?>"><?= $response['fluency'] ?>%</span>
                </div>
                <div class="metric-mini">
                    <span class="metric-label">Релевант.</span>
                    <span class="metric-value <?= $response['relevance'] >= 80 ? 'high' : ($response['relevance'] >= 60 ? 'medium' : 'low') ?>"><?= $response['relevance'] ?>%</span>
                </div>
                <div class="metric-mini avg-score">
                    <span class="metric-label">Среднее</span>
                    <span class="metric-value <?= $response['avgScore'] >= 80 ? 'high' : ($response['avgScore'] >= 60 ? 'medium' : 'low') ?>"><?= $response['avgScore'] ?>%</span>
                </div>
            </div>
        </div>
        <?php endforeach; ?>

        <?php if (empty($responses)): ?>
        <div class="empty-state">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>
            </svg>
            <p>Нет ответов</p>
        </div>
        <?php endif; ?>
    </div>

    <?php if ($hasMore): ?>
    <div class="load-more-container">
        <button type="button" class="btn-load-more" id="loadMoreResponses" data-offset="20">
            <span class="btn-text">Загрузить ещё</span>
            <span class="btn-loader" style="display:none;">
                <svg class="spinner" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" fill="none" stroke-dasharray="30 60"/></svg>
            </span>
        </button>
    </div>
    <?php endif; ?>
</div>

<style>
/* Model Comparison */
.model-comparison {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 16px;
}
.model-card {
    background: var(--bg-secondary);
    border-radius: var(--radius-md);
    padding: 20px;
}
.model-name {
    font-size: 14px;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 8px;
}
.model-score {
    font-size: 32px;
    font-weight: 700;
    color: var(--accent-primary);
    font-family: 'JetBrains Mono', monospace;
}
.model-meta {
    font-size: 12px;
    color: var(--text-tertiary);
    margin-top: 4px;
    margin-bottom: 12px;
}
.model-bar {
    height: 4px;
    background: var(--bg-tertiary);
    border-radius: 2px;
    overflow: hidden;
}
.model-bar-fill {
    height: 100%;
    background: var(--accent-primary);
    border-radius: 2px;
    transition: width 0.5s ease;
}

.responses-list {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.response-card {
    background: var(--bg-card);
    border: 1px solid var(--border-subtle);
    border-radius: var(--radius-lg);
    padding: 20px;
    transition: all var(--transition-fast);
}
.response-card:hover {
    border-color: var(--accent-primary);
    box-shadow: 0 4px 20px rgba(139, 92, 246, 0.1);
}

.response-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 12px;
    flex-wrap: wrap;
}

.project-link {
    color: var(--accent-primary);
    text-decoration: none;
    font-size: 13px;
}
.project-link:hover { text-decoration: underline; }

.response-date {
    color: var(--text-tertiary);
    font-size: 12px;
    margin-left: auto;
}

.response-prompt {
    font-size: 13px;
    color: var(--text-secondary);
    margin-bottom: 8px;
    padding: 8px 12px;
    background: var(--bg-secondary);
    border-radius: var(--radius-md);
}
.response-prompt strong {
    color: var(--text-primary);
}

.response-text {
    font-size: 14px;
    line-height: 1.6;
    color: var(--text-primary);
    margin-bottom: 16px;
}

.response-metrics {
    display: flex;
    gap: 16px;
    flex-wrap: wrap;
    padding-top: 12px;
    border-top: 1px solid var(--border-subtle);
}

.metric-mini {
    display: flex;
    flex-direction: column;
    gap: 4px;
}
.metric-mini .metric-label {
    font-size: 11px;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.metric-mini .metric-value {
    font-family: 'JetBrains Mono', monospace;
    font-size: 14px;
    font-weight: 600;
}
.metric-mini .metric-value.high { color: var(--success); }
.metric-mini .metric-value.medium { color: var(--warning); }
.metric-mini .metric-value.low { color: var(--danger); }

.metric-mini.avg-score {
    margin-left: auto;
    padding-left: 16px;
    border-left: 1px solid var(--border-subtle);
}
.metric-mini.avg-score .metric-value {
    font-size: 16px;
}

.load-more-container {
    display: flex;
    justify-content: center;
    margin-top: 24px;
}

.btn-load-more {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 12px 32px;
    background: var(--bg-secondary);
    border: 1px solid var(--border-subtle);
    border-radius: var(--radius-md);
    color: var(--text-primary);
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all var(--transition-fast);
}
.btn-load-more:hover {
    background: var(--bg-card-hover);
    border-color: var(--accent-primary);
}
.btn-load-more:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}
.btn-load-more .spinner {
    width: 16px;
    height: 16px;
    animation: spin 1s linear infinite;
}
@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: var(--text-tertiary);
}
.empty-state svg {
    width: 48px;
    height: 48px;
    margin-bottom: 16px;
    opacity: 0.5;
}
</style>

<?php
$pageScripts = <<<'SCRIPTS'
<script>
document.addEventListener('DOMContentLoaded', function() {
    const loadMoreBtn = document.getElementById('loadMoreResponses');
    const responsesList = document.getElementById('responsesList');

    if (!loadMoreBtn) return;

    loadMoreBtn.addEventListener('click', async function() {
        const offset = parseInt(this.dataset.offset);
        const btnText = this.querySelector('.btn-text');
        const btnLoader = this.querySelector('.btn-loader');

        // Show loading state
        btnText.style.display = 'none';
        btnLoader.style.display = 'block';
        this.disabled = true;

        try {
            const resp = await fetch(`/api/responses?offset=${offset}&limit=20`);
            const result = await resp.json();

            if (result.success && result.data.responses.length > 0) {
                // Append new responses
                result.data.responses.forEach(response => {
                    const card = createResponseCard(response);
                    responsesList.insertBefore(card, loadMoreBtn.parentElement);
                });

                // Update offset
                this.dataset.offset = offset + 20;

                // Hide button if no more data
                if (!result.data.hasMore) {
                    this.parentElement.remove();
                }
            } else {
                this.parentElement.remove();
            }
        } catch (err) {
            console.error('Error loading more responses:', err);
        }

        // Reset button state
        btnText.style.display = 'block';
        btnLoader.style.display = 'none';
        this.disabled = false;
    });

    function createResponseCard(response) {
        const toneLabels = { positive: 'Позитивный', neutral: 'Нейтральный', negative: 'Негативный' };
        const getScoreClass = (score) => score >= 80 ? 'high' : (score >= 60 ? 'medium' : 'low');

        const card = document.createElement('div');
        card.className = 'response-card';
        card.innerHTML = `
            <div class="response-header">
                <span class="llm-badge ${response.model.toLowerCase()}">${response.model.toUpperCase()}</span>
                <a href="/topics/${response.projectId}" class="project-link">${escapeHtml(response.projectName)}</a>
                <span class="response-date">${response.date}</span>
                <span class="tone-badge ${response.tone}">${toneLabels[response.tone] || response.tone}</span>
            </div>
            <div class="response-prompt">
                <strong>Промт:</strong> ${escapeHtml(response.prompt)}
            </div>
            <div class="response-text">${escapeHtml(response.response)}</div>
            <div class="response-metrics">
                <div class="metric-mini">
                    <span class="metric-label">Связн.</span>
                    <span class="metric-value ${getScoreClass(response.coherence)}">${response.coherence}%</span>
                </div>
                <div class="metric-mini">
                    <span class="metric-label">Согласов.</span>
                    <span class="metric-value ${getScoreClass(response.consistency)}">${response.consistency}%</span>
                </div>
                <div class="metric-mini">
                    <span class="metric-label">Беглость</span>
                    <span class="metric-value ${getScoreClass(response.fluency)}">${response.fluency}%</span>
                </div>
                <div class="metric-mini">
                    <span class="metric-label">Релевант.</span>
                    <span class="metric-value ${getScoreClass(response.relevance)}">${response.relevance}%</span>
                </div>
                <div class="metric-mini avg-score">
                    <span class="metric-label">Среднее</span>
                    <span class="metric-value ${getScoreClass(response.avgScore)}">${response.avgScore}%</span>
                </div>
            </div>
        `;
        return card;
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
});
</script>
SCRIPTS;
?>
