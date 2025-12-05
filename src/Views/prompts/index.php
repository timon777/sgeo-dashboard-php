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
            <?php foreach ($prompts as $prompt): ?>
            <tr class="prompt-row"
                data-id="<?= htmlspecialchars($prompt['id']) ?>"
                data-llm="<?= strtolower($prompt['llm']) ?>"
                data-tone="<?= $prompt['tone'] ?>"
                data-text="<?= strtolower(htmlspecialchars($prompt['prompt'])) ?>"
                data-score="<?= $prompt['score'] ?>"
                data-risk="<?= $prompt['risk'] ?>"
                data-date="<?= date('d.m.Y', strtotime($prompt['date'])) ?>"
                data-full-prompt="<?= htmlspecialchars($prompt['full_prompt'] ?? $prompt['prompt']) ?>">
                <td>
                    <div class="prompt-cell"><?= htmlspecialchars($prompt['prompt']) ?></div>
                </td>
                <td>
                    <span class="llm-badge <?= $prompt['llm'] ?>"><?= strtoupper($prompt['llm']) ?></span>
                </td>
                <td>
                    <div class="response-cell"><?= htmlspecialchars($prompt['response']) ?></div>
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
                    <span class="score-value <?= $prompt['score'] >= 80 ? 'high' : ($prompt['score'] >= 60 ? 'medium' : 'low') ?>">
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
                <td class="date-cell"><?= date('d.m.Y', strtotime($prompt['date'])) ?></td>
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

    <?php if ($paginationPage > 3): ?>
        <a href="?page=1" class="pagination-btn">1</a>
        <?php if ($paginationPage > 4): ?><span class="pagination-btn disabled">...</span><?php endif; ?>
    <?php endif; ?>

    <?php
    $startPage = max(1, $paginationPage - 2);
    $endPage = min($totalPages, $paginationPage + 2);
    for ($i = $startPage; $i <= $endPage; $i++): ?>
        <?php if ($i == $paginationPage): ?>
            <span class="pagination-btn active"><?= $i ?></span>
        <?php else: ?>
            <a href="?page=<?= $i ?>" class="pagination-btn"><?= $i ?></a>
        <?php endif; ?>
    <?php endfor; ?>

    <?php if ($paginationPage < $totalPages - 2): ?>
        <?php if ($paginationPage < $totalPages - 3): ?><span class="pagination-btn disabled">...</span><?php endif; ?>
        <a href="?page=<?= $totalPages ?>" class="pagination-btn"><?= $totalPages ?></a>
    <?php endif; ?>

    <?php if ($paginationPage < $totalPages): ?>
        <a href="?page=<?= $paginationPage + 1 ?>" class="pagination-btn">&gt;</a>
    <?php else: ?>
        <span class="pagination-btn disabled">&gt;</span>
    <?php endif; ?>
</div>
<?php endif; ?>

<div class="prompts-info">
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
            <div class="modal-loading" id="modal-loading">Загрузка...</div>
            <div class="modal-content" id="modal-content" style="display: none;">
                <div class="detail-section">
                    <div class="detail-label">Промт</div>
                    <div class="detail-content" id="modal-prompt"></div>
                </div>
                <div class="detail-section">
                    <div class="detail-label">Ответ LLM</div>
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
</div>

<style>
.prompt-row { cursor: pointer; transition: background var(--transition-fast); }
.prompt-row:hover { background: var(--bg-card-hover); }
.prompt-cell, .response-cell {
    max-width: 250px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.response-cell { color: var(--text-tertiary); max-width: 200px; }
.score-value {
    font-family: 'JetBrains Mono', monospace;
    font-weight: 600;
}
.score-value.high { color: var(--success); }
.score-value.medium { color: var(--warning); }
.score-value.low { color: var(--danger); }
.date-cell { color: var(--text-tertiary); font-size: 13px; }
.prompts-info {
    text-align: center;
    margin-top: 16px;
    color: var(--text-tertiary);
    font-size: 13px;
}

/* Modal */
.modal-overlay {
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
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
.modal-title { font-size: 18px; font-weight: 600; color: var(--text-primary); margin: 0; }
.modal-body { padding: 24px 28px; overflow-y: auto; flex: 1; }
.modal-loading { text-align: center; padding: 40px; color: var(--text-tertiary); }
.detail-section { margin-bottom: 24px; }
.detail-label {
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
.detail-content.response { color: var(--text-secondary); background: var(--bg-tertiary); }
.detail-meta {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    gap: 16px;
    padding-top: 16px;
    border-top: 1px solid var(--border-subtle);
}
.meta-item { display: flex; flex-direction: column; gap: 6px; }
.meta-label {
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--text-muted);
}
.btn-sm { padding: 8px 14px; font-size: 13px; }
.close-modal {
    width: 32px; height: 32px;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    line-height: 1;
}
</style>

<?php
$pageScripts = <<<'SCRIPTS'
<script>
document.addEventListener('DOMContentLoaded', function() {
    const toneLabels = { positive: 'Позитивная', neutral: 'Нейтральная', negative: 'Негативная' };
    const riskLabels = { low: 'Низкий', medium: 'Средний', high: 'Высокий' };

    const modal = document.getElementById('prompt-modal');
    const modalLoading = document.getElementById('modal-loading');
    const modalContent = document.getElementById('modal-content');

    // Click on row to open modal - load full response via AJAX
    document.querySelectorAll('.prompt-row').forEach(row => {
        row.addEventListener('click', async function() {
            const id = this.dataset.id;
            const llm = this.dataset.llm.toUpperCase();
            const tone = this.dataset.tone;
            const score = this.dataset.score;
            const risk = this.dataset.risk;
            const date = this.dataset.date;
            const fullPrompt = this.dataset.fullPrompt;

            // Show modal with loading state
            modal.classList.add('show');
            modalLoading.style.display = 'block';
            modalContent.style.display = 'none';

            // Set metadata immediately (from data attributes)
            document.getElementById('modal-llm').textContent = llm;
            document.getElementById('modal-llm').className = 'llm-badge ' + llm.toLowerCase();
            document.getElementById('modal-tone').textContent = toneLabels[tone] || tone;
            document.getElementById('modal-tone').className = 'tone-badge ' + tone;
            document.getElementById('modal-score').textContent = score;
            document.getElementById('modal-score').style.color = score >= 80 ? 'var(--success)' : (score >= 60 ? 'var(--warning)' : 'var(--danger)');
            document.getElementById('modal-risk').textContent = riskLabels[risk] || risk;
            document.getElementById('modal-risk').className = 'risk-badge ' + risk;
            document.getElementById('modal-date').textContent = date;

            // Load full response via AJAX
            try {
                const resp = await fetch(`/api/prompts/${id}`);
                if (resp.ok) {
                    const data = await resp.json();
                    document.getElementById('modal-prompt').textContent = data.prompt || fullPrompt;
                    document.getElementById('modal-response').textContent = data.response || 'Нет ответа';
                } else {
                    document.getElementById('modal-prompt').textContent = fullPrompt;
                    document.getElementById('modal-response').textContent = 'Не удалось загрузить ответ';
                }
            } catch (e) {
                document.getElementById('modal-prompt').textContent = fullPrompt;
                document.getElementById('modal-response').textContent = 'Ошибка загрузки';
            }

            modalLoading.style.display = 'none';
            modalContent.style.display = 'block';
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

    llmFilter.addEventListener('change', applyFilters);
    toneFilter.addEventListener('change', applyFilters);
    searchInput.addEventListener('input', applyFilters);
});
</script>
SCRIPTS;
?>
