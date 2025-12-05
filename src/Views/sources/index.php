<div class="page-header animate-on-scroll">
    <div class="page-header-top">
        <div class="page-title-group">
            <h1 class="page-title">Источники информации</h1>
            <p class="page-subtitle">Анализ и оценка качества источников по методологии E-E-A-T</p>
        </div>
        <div class="page-actions">
            <a href="/export/sources" class="btn btn-secondary">
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

<!-- Analysis Charts -->
<div class="sources-section">
    <div class="source-card">
        <div class="source-title">E-E-A-T оценка</div>
        <div class="source-subtitle">Средние показатели по всем источникам</div>
        <div class="radar-container">
            <canvas id="eeatRadarChart"></canvas>
        </div>
    </div>

    <div class="source-card">
        <div class="source-title">География источников</div>
        <div class="source-subtitle">Распределение по странам</div>
        <div class="pie-container">
            <canvas id="geoPieChart"></canvas>
        </div>
        <div class="legend">
            <div class="legend-item">
                <span class="legend-dot" style="background: #6366f1;"></span>
                <span>55% — Казахстан</span>
            </div>
            <div class="legend-item">
                <span class="legend-dot" style="background: #22c55e;"></span>
                <span>30% — Россия</span>
            </div>
            <div class="legend-item">
                <span class="legend-dot" style="background: #f59e0b;"></span>
                <span>15% — США</span>
            </div>
        </div>
    </div>

    <div class="source-card">
        <div class="source-title">Типология</div>
        <div class="source-subtitle">Распределение по типам площадок</div>
        <div class="pie-container">
            <canvas id="typePieChart"></canvas>
        </div>
        <div class="legend">
            <div class="legend-item">
                <span class="legend-dot" style="background: #6366f1;"></span>
                <span>Гос. сайты</span>
            </div>
            <div class="legend-item">
                <span class="legend-dot" style="background: #3b82f6;"></span>
                <span>СМИ</span>
            </div>
            <div class="legend-item">
                <span class="legend-dot" style="background: #8b5cf6;"></span>
                <span>Аналитика</span>
            </div>
            <div class="legend-item">
                <span class="legend-dot" style="background: #f59e0b;"></span>
                <span>Wiki</span>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="filters-row" id="source-filters">
    <button class="filter-btn active" data-filter="all">Все типы</button>
    <button class="filter-btn" data-filter="gov">Гос. сайты</button>
    <button class="filter-btn" data-filter="media">СМИ</button>
    <button class="filter-btn" data-filter="analytics">Аналитика</button>
    <button class="filter-btn" data-filter="wiki">Wiki</button>
    <div style="flex: 1;"></div>
    <input type="text" class="input-field" id="source-search" placeholder="Поиск по домену..." style="width: auto; min-width: 200px;">
</div>

<!-- Sources Table -->
<div class="table-container" style="overflow-x: auto;">
    <table class="table">
        <thead>
            <tr>
                <th class="sortable">Домен</th>
                <th class="sortable">Тип</th>
                <th class="sortable">Страна</th>
                <th class="sortable">Экспертиза</th>
                <th class="sortable">Опыт</th>
                <th class="sortable">Авторитет</th>
                <th class="sortable">Доверие</th>
                <th class="sortable">E-E-A-T</th>
                <th class="sortable">Доля %</th>
                <th>Автор</th>
                <th>HTTPS</th>
            </tr>
        </thead>
        <tbody id="sources-table">
            <?php foreach ($sources as $source): ?>
            <tr data-type="<?= $source['type'] ?>" data-domain="<?= strtolower($source['domain']) ?>">
                <td>
                    <a href="https://<?= htmlspecialchars($source['domain']) ?>" target="_blank" style="color: var(--accent-primary); text-decoration: none;">
                        <?= htmlspecialchars($source['domain']) ?>
                    </a>
                </td>
                <td>
                    <span class="type-badge <?= $source['type'] ?>">
                        <?php
                        $typeLabels = ['gov' => 'Гос.', 'media' => 'СМИ', 'analytics' => 'Аналит.', 'wiki' => 'Wiki'];
                        echo $typeLabels[$source['type']] ?? $source['type'];
                        ?>
                    </span>
                </td>
                <td><?= htmlspecialchars($source['country']) ?></td>
                <td style="font-family: 'JetBrains Mono', monospace;"><?= $source['expertise'] ?></td>
                <td style="font-family: 'JetBrains Mono', monospace;"><?= $source['experience'] ?></td>
                <td style="font-family: 'JetBrains Mono', monospace;"><?= $source['authority'] ?></td>
                <td style="font-family: 'JetBrains Mono', monospace;"><?= $source['trust'] ?></td>
                <td>
                    <span style="font-family: 'JetBrains Mono', monospace; font-weight: 600; color: <?= $source['eeat'] >= 85 ? 'var(--success)' : ($source['eeat'] >= 70 ? 'var(--warning)' : 'var(--danger)') ?>;">
                        <?= $source['eeat'] ?>
                    </span>
                </td>
                <td style="font-family: 'JetBrains Mono', monospace;"><?= number_format($source['share'], 1) ?>%</td>
                <td>
                    <?php if ($source['author']): ?>
                        <span style="color: var(--success);">✓</span>
                    <?php else: ?>
                        <span style="color: var(--text-muted);">—</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($source['https']): ?>
                        <span style="color: var(--success);">✓</span>
                    <?php else: ?>
                        <span style="color: var(--danger);">✗</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Pagination -->
<div class="pagination">
    <button class="pagination-btn">&lt;</button>
    <button class="pagination-btn active">1</button>
    <button class="pagination-btn">2</button>
    <button class="pagination-btn">3</button>
    <button class="pagination-btn">...</button>
    <button class="pagination-btn">124</button>
    <button class="pagination-btn">&gt;</button>
</div>

<div style="text-align: center; margin-top: 16px; color: var(--text-tertiary); font-size: 13px;">
    Всего источников: <?= number_format($totalSources) ?>
</div>

<?php
$pageScripts = <<<'SCRIPTS'
<script>
function initSourcesCharts() {
    const themeColors = getChartColors();
    updateChartDefaults();

    const colors = {
        primary: '#6366f1',
        success: '#22c55e',
        warning: '#f59e0b',
        info: '#3b82f6',
        purple: '#8b5cf6'
    };

    // E-E-A-T Radar Chart
    pageCharts.eeat = new Chart(document.getElementById('eeatRadarChart'), {
        type: 'radar',
        data: {
            labels: ['Экспертиза', 'Опыт', 'Авторитетность', 'Надёжность'],
            datasets: [{
                data: [82, 78, 85, 80],
                backgroundColor: 'rgba(139, 92, 246, 0.2)',
                borderColor: colors.purple,
                borderWidth: 2,
                pointBackgroundColor: colors.purple,
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

    // Geography Pie Chart
    pageCharts.geo = new Chart(document.getElementById('geoPieChart'), {
        type: 'doughnut',
        data: {
            labels: ['Казахстан', 'Россия', 'США'],
            datasets: [{ data: [55, 30, 15], backgroundColor: [colors.primary, colors.success, colors.warning], borderWidth: 0 }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, cutout: '65%' }
    });

    // Type Pie Chart
    pageCharts.type = new Chart(document.getElementById('typePieChart'), {
        type: 'doughnut',
        data: {
            labels: ['Гос. сайты', 'СМИ', 'Аналитика', 'Wiki'],
            datasets: [{ data: [25, 40, 20, 15], backgroundColor: [colors.primary, colors.info, colors.purple, colors.warning], borderWidth: 0 }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, cutout: '65%' }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    initSourcesCharts();

    // Filter functionality
    const filterBtns = document.querySelectorAll('#source-filters .filter-btn');
    const searchInput = document.getElementById('source-search');
    const rows = document.querySelectorAll('#sources-table tr');
    let currentFilter = 'all';

    filterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            filterBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            currentFilter = this.dataset.filter;
            applyFilters();
        });
    });

    searchInput.addEventListener('input', applyFilters);

    function applyFilters() {
        const searchTerm = searchInput.value.toLowerCase();
        rows.forEach(row => {
            const matchesType = currentFilter === 'all' || row.dataset.type === currentFilter;
            const matchesSearch = !searchTerm || row.dataset.domain.includes(searchTerm);
            row.style.display = matchesType && matchesSearch ? '' : 'none';
        });
    }
});
</script>
SCRIPTS;
?>
