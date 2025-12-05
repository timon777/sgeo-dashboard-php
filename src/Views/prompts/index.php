<div class="page-header animate-on-scroll">
    <div class="page-header-top">
        <div class="page-title-group">
            <h1 class="page-title">Промты и ответы LLM</h1>
            <p class="page-subtitle">Анализ запросов и качества генерируемых ответов</p>
        </div>
        <div class="page-actions">
            <a href="/export/prompts" class="btn btn-secondary">
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

<!-- Quality Radar Chart -->
<div class="analysis-grid" style="margin-bottom: 32px;">
    <div class="analysis-card">
        <div class="analysis-title">Качество промтов</div>
        <div class="analysis-subtitle">Оценка формулировки запросов</div>
        <div class="radar-container">
            <canvas id="promptsRadarChart"></canvas>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="filters-row" id="prompts-filters">
    <select class="select-field" id="llm-filter" style="width: auto; min-width: 150px;">
        <option value="all">Все LLM</option>
        <option value="gpt">ChatGPT</option>
        <option value="deepseek">DeepSeek</option>
        <option value="grok">Grok</option>
        <option value="gemini">Gemini</option>
        <option value="perplexity">Perplexity</option>
        <option value="claude">Claude</option>
    </select>
    <select class="select-field" id="tone-filter" style="width: auto; min-width: 150px;">
        <option value="all">Все тональности</option>
        <option value="positive">Позитивная</option>
        <option value="neutral">Нейтральная</option>
        <option value="negative">Негативная</option>
    </select>
    <div class="search-filter" style="flex: 1; max-width: 300px;">
        <input type="text" class="input-field" id="prompts-search" placeholder="Поиск по промтам...">
    </div>
</div>

<!-- Prompts Table -->
<div class="table-container">
    <table class="table">
        <thead>
            <tr>
                <th class="sortable">Промт</th>
                <th class="sortable">LLM</th>
                <th>Ответ</th>
                <th class="sortable">Тональность</th>
                <th class="sortable">Балл</th>
                <th class="sortable">Риск</th>
                <th class="sortable">Дата</th>
            </tr>
        </thead>
        <tbody id="prompts-table">
            <?php foreach ($prompts as $index => $prompt): ?>
            <tr class="prompt-row"
                data-llm="<?= strtolower($prompt['llm']) ?>"
                data-tone="<?= $prompt['tone'] ?>"
                data-text="<?= strtolower(htmlspecialchars($prompt['prompt'])) ?>"
                data-index="<?= $index ?>">
                <td>
                    <div class="prompt-cell" style="max-width: 250px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; cursor: pointer;">
                        <?= htmlspecialchars($prompt['prompt']) ?>
                    </div>
                </td>
                <td>
                    <span class="llm-badge <?= $prompt['llm'] ?>"><?= strtoupper($prompt['llm']) ?></span>
                </td>
                <td>
                    <div class="response-cell" style="max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; color: var(--text-tertiary); cursor: pointer;">
                        <?= htmlspecialchars($prompt['response']) ?>
                    </div>
                </td>
                <td>
                    <span class="tone-badge <?= $prompt['tone'] ?>">
                        <?php
                        $toneLabels = ['positive' => 'Позитивная', 'neutral' => 'Нейтральная', 'negative' => 'Негативная'];
                        echo $toneLabels[$prompt['tone']] ?? $prompt['tone'];
                        ?>
                    </span>
                </td>
                <td>
                    <span style="font-family: 'JetBrains Mono', monospace; font-weight: 600; color: <?= $prompt['score'] >= 80 ? 'var(--success)' : ($prompt['score'] >= 60 ? 'var(--warning)' : 'var(--danger)') ?>;">
                        <?= $prompt['score'] ?>
                    </span>
                </td>
                <td>
                    <span class="risk-badge <?= $prompt['risk'] ?>">
                        <?php
                        $riskLabels = ['low' => 'Низкий', 'medium' => 'Средний', 'high' => 'Высокий'];
                        echo $riskLabels[$prompt['risk']] ?? $prompt['risk'];
                        ?>
                    </span>
                </td>
                <td style="color: var(--text-tertiary); font-size: 13px;">
                    <?= date('d.m.Y', strtotime($prompt['date'])) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Pagination -->
<?php if ($totalPages > 1): ?>
<div class="pagination">
    <?php if ($paginationPage > 1): ?>
        <a href="?page=<?= $paginationPage - 1 ?>" class="pagination-btn">&lt;</a>
    <?php else: ?>
        <span class="pagination-btn disabled">&lt;</span>
    <?php endif; ?>

    <?php
    // Show first page
    if ($paginationPage > 3): ?>
        <a href="?page=1" class="pagination-btn">1</a>
        <?php if ($paginationPage > 4): ?>
            <span class="pagination-btn disabled">...</span>
        <?php endif; ?>
    <?php endif; ?>

    <?php
    // Show pages around current
    $startPage = max(1, $paginationPage - 2);
    $endPage = min($totalPages, $paginationPage + 2);
    for ($i = $startPage; $i <= $endPage; $i++): ?>
        <?php if ($i == $paginationPage): ?>
            <span class="pagination-btn active"><?= $i ?></span>
        <?php else: ?>
            <a href="?page=<?= $i ?>" class="pagination-btn"><?= $i ?></a>
        <?php endif; ?>
    <?php endfor; ?>

    <?php
    // Show last page
    if ($paginationPage < $totalPages - 2): ?>
        <?php if ($paginationPage < $totalPages - 3): ?>
            <span class="pagination-btn disabled">...</span>
        <?php endif; ?>
        <a href="?page=<?= $totalPages ?>" class="pagination-btn"><?= $totalPages ?></a>
    <?php endif; ?>

    <?php if ($paginationPage < $totalPages): ?>
        <a href="?page=<?= $paginationPage + 1 ?>" class="pagination-btn">&gt;</a>
    <?php else: ?>
        <span class="pagination-btn disabled">&gt;</span>
    <?php endif; ?>
</div>
<?php endif; ?>

<div style="text-align: center; margin-top: 16px; color: var(--text-tertiary); font-size: 13px;">
    Показано <?= count($prompts) ?> из <?= number_format($totalPrompts) ?> записей
    <?php if ($totalPages > 1): ?>(страница <?= $paginationPage ?> из <?= $totalPages ?>)<?php endif; ?>
</div>

<!-- Prompt Detail Modal -->
<div class="modal-overlay" id="prompt-modal">
    <div class="modal prompt-detail-modal">
        <div class="modal-header">
            <h3 class="modal-title">Детали промта</h3>
            <button class="btn btn-secondary btn-sm close-modal">&times;</button>
        </div>
        <div class="modal-body">
            <div class="detail-section">
                <div class="detail-label">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 16px; height: 16px;">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                    </svg>
                    Промт
                </div>
                <div class="detail-content" id="modal-prompt"></div>
            </div>
            <div class="detail-section">
                <div class="detail-label">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 16px; height: 16px;">
                        <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>
                    </svg>
                    Ответ LLM
                </div>
                <div class="detail-content response" id="modal-response"></div>
            </div>
            <div class="detail-meta">
                <div class="meta-item">
                    <span class="meta-label">LLM:</span>
                    <span id="modal-llm" class="llm-badge"></span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Тональность:</span>
                    <span id="modal-tone" class="tone-badge"></span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Балл:</span>
                    <span id="modal-score"></span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Риск:</span>
                    <span id="modal-risk" class="risk-badge"></span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Дата:</span>
                    <span id="modal-date"></span>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.prompt-row {
    cursor: pointer;
    transition: background var(--transition-fast);
}
.prompt-row:hover {
    background: var(--bg-card-hover);
}

/* Modal styles */
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.7);
    z-index: 1000;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 20px;
}
.modal-overlay.show { display: flex; }

.prompt-detail-modal {
    background: var(--bg-card);
    border-radius: var(--radius-xl);
    max-width: 700px;
    width: 100%;
    max-height: 80vh;
    overflow: hidden;
    display: flex;
    flex-direction: column;
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 24px 28px;
    border-bottom: 1px solid var(--border-subtle);
}

.modal-title {
    font-size: 18px;
    font-weight: 600;
    color: var(--text-primary);
    margin: 0;
}

.modal-body {
    padding: 24px 28px;
    overflow-y: auto;
    flex: 1;
}

.detail-section {
    margin-bottom: 24px;
}

.detail-label {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--text-tertiary);
    margin-bottom: 12px;
}

.detail-content {
    background: var(--bg-secondary);
    border-radius: var(--radius-md);
    padding: 16px;
    font-size: 14px;
    line-height: 1.7;
    color: var(--text-primary);
    white-space: pre-wrap;
    word-break: break-word;
    max-height: 200px;
    overflow-y: auto;
}

.detail-content.response {
    color: var(--text-secondary);
    background: var(--bg-tertiary);
}

.detail-meta {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    gap: 16px;
    padding-top: 16px;
    border-top: 1px solid var(--border-subtle);
}

.meta-item {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.meta-label {
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--text-muted);
}

.btn-sm {
    padding: 8px 14px;
    font-size: 13px;
}

.close-modal {
    width: 32px;
    height: 32px;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    line-height: 1;
}
</style>

<!-- Store prompt data for modal -->
<script type="application/json" id="prompts-data">
<?= json_encode(array_map(function($p) {
    return [
        'prompt' => $p['prompt'],
        'response' => $p['full_response'] ?? $p['response'],
        'llm' => strtoupper($p['llm']),
        'tone' => $p['tone'],
        'score' => $p['score'],
        'risk' => $p['risk'],
        'date' => date('d.m.Y', strtotime($p['date']))
    ];
}, $prompts), JSON_UNESCAPED_UNICODE) ?>
</script>

<?php
$pageScripts = <<<'SCRIPTS'
<script>
function initPromptsCharts() {
    const themeColors = getChartColors();
    updateChartDefaults();

    pageCharts.radar = new Chart(document.getElementById('promptsRadarChart'), {
        type: 'radar',
        data: {
            labels: ['Конкретность', 'Полнота задания', 'Нейтральность', 'Однозначность', 'Тип задачи'],
            datasets: [{
                data: [75, 82, 68, 78, 70],
                backgroundColor: 'rgba(34, 197, 94, 0.2)',
                borderColor: '#22c55e',
                borderWidth: 2,
                pointBackgroundColor: '#22c55e',
                pointBorderColor: isLightTheme() ? '#1a1c22' : '#fff',
                pointBorderWidth: 1,
                pointRadius: 4
            }]
        },
        options: {
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
                    pointLabels: { font: { size: 11 }, color: themeColors.textStrong }
                }
            }
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    initPromptsCharts();

    // Load prompt data
    const promptsData = JSON.parse(document.getElementById('prompts-data').textContent);
    const toneLabels = { positive: 'Позитивная', neutral: 'Нейтральная', negative: 'Негативная' };
    const riskLabels = { low: 'Низкий', medium: 'Средний', high: 'Высокий' };

    // Modal elements
    const modal = document.getElementById('prompt-modal');
    const modalPrompt = document.getElementById('modal-prompt');
    const modalResponse = document.getElementById('modal-response');
    const modalLlm = document.getElementById('modal-llm');
    const modalTone = document.getElementById('modal-tone');
    const modalScore = document.getElementById('modal-score');
    const modalRisk = document.getElementById('modal-risk');
    const modalDate = document.getElementById('modal-date');

    // Click on row to open modal
    document.querySelectorAll('.prompt-row').forEach(row => {
        row.addEventListener('click', function() {
            const index = parseInt(this.dataset.index);
            const data = promptsData[index];
            if (!data) return;

            modalPrompt.textContent = data.prompt;
            modalResponse.textContent = data.response;
            modalLlm.textContent = data.llm;
            modalLlm.className = 'llm-badge ' + data.llm.toLowerCase();
            modalTone.textContent = toneLabels[data.tone] || data.tone;
            modalTone.className = 'tone-badge ' + data.tone;
            modalScore.textContent = data.score;
            modalScore.style.color = data.score >= 80 ? 'var(--success)' : (data.score >= 60 ? 'var(--warning)' : 'var(--danger)');
            modalRisk.textContent = riskLabels[data.risk] || data.risk;
            modalRisk.className = 'risk-badge ' + data.risk;
            modalDate.textContent = data.date;

            modal.classList.add('show');
        });
    });

    // Close modal
    modal.querySelector('.close-modal').addEventListener('click', () => modal.classList.remove('show'));
    modal.addEventListener('click', function(e) {
        if (e.target === this) this.classList.remove('show');
    });

    // Filter functionality
    const llmFilter = document.getElementById('llm-filter');
    const toneFilter = document.getElementById('tone-filter');
    const searchInput = document.getElementById('prompts-search');
    const rows = document.querySelectorAll('#prompts-table tr');

    llmFilter.addEventListener('change', applyFilters);
    toneFilter.addEventListener('change', applyFilters);
    searchInput.addEventListener('input', applyFilters);

    function applyFilters() {
        const llm = llmFilter.value;
        const tone = toneFilter.value;
        const search = searchInput.value.toLowerCase();

        rows.forEach(row => {
            const matchesLlm = llm === 'all' || row.dataset.llm === llm;
            const matchesTone = tone === 'all' || row.dataset.tone === tone;
            const matchesSearch = !search || row.dataset.text.includes(search);
            row.style.display = matchesLlm && matchesTone && matchesSearch ? '' : 'none';
        });
    }
});
</script>
SCRIPTS;
?>
