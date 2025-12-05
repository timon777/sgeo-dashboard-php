<div class="page-header animate-on-scroll">
    <div class="page-header-top">
        <div class="page-title-group">
            <h1 class="page-title">Промты и ответы LLM</h1>
            <p class="page-subtitle">Анализ запросов и качества генерируемых ответов</p>
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
                Добавить промт
            </button>
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
        <option value="chatgpt">ChatGPT</option>
        <option value="deepseek">DeepSeek</option>
        <option value="grok">Grok</option>
        <option value="gemini">Gemini</option>
        <option value="perplexity">Perplexity</option>
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
            <tr data-llm="<?= strtolower($prompt['llm']) ?>" data-tone="<?= $prompt['tone'] ?>" data-text="<?= strtolower(htmlspecialchars($prompt['prompt'])) ?>">
                <td>
                    <div style="max-width: 250px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        <?= htmlspecialchars($prompt['prompt']) ?>
                    </div>
                </td>
                <td>
                    <span class="llm-badge <?= $prompt['llm'] ?>"><?= strtoupper($prompt['llm']) ?></span>
                </td>
                <td>
                    <div style="max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; color: var(--text-tertiary);">
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
