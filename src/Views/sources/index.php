<div class="page-header animate-on-scroll">
    <div class="page-header-top">
        <div class="page-title-group">
            <h1 class="page-title">Источники информации</h1>
            <p class="page-subtitle">Анализ и оценка качества источников по методологии E-E-A-T</p>
        </div>
        <div class="page-actions">
            <button class="btn btn-secondary" id="import-csv-btn">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                    <polyline points="17,8 12,3 7,8"/>
                    <line x1="12" y1="3" x2="12" y2="15"/>
                </svg>
                Импорт CSV
            </button>
            <a href="/export/sources" class="btn btn-secondary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                    <polyline points="7,10 12,15 17,10"/>
                    <line x1="12" y1="15" x2="12" y2="3"/>
                </svg>
                Экспорт
            </a>
            <button class="btn btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="5" x2="12" y2="19"/>
                    <line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                Добавить источник
            </button>
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
<div class="filters-row">
    <button class="filter-btn active" data-group="type">Все типы</button>
    <button class="filter-btn" data-group="type">Гос. сайты</button>
    <button class="filter-btn" data-group="type">СМИ</button>
    <button class="filter-btn" data-group="type">Аналитика</button>
    <button class="filter-btn" data-group="type">Wiki</button>
    <div style="flex: 1;"></div>
    <input type="text" class="input-field" placeholder="Поиск по домену..." style="width: auto; min-width: 200px;">
</div>

<!-- Sources Table -->
<div class="table-container" style="overflow-x: auto;">
    <table class="table">
        <thead>
            <tr>
                <th class="sortable">Домен</th>
                <th class="sortable">Тип</th>
                <th class="sortable">Страна</th>
                <th class="sortable">URL Rating</th>
                <th class="sortable">Domain Rating</th>
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
        <tbody>
            <?php foreach ($sources as $source): ?>
            <tr>
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
                <td style="font-family: 'JetBrains Mono', monospace;"><?= $source['url_rating'] ?></td>
                <td style="font-family: 'JetBrains Mono', monospace;"><?= $source['domain_rating'] ?></td>
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

<!-- CSV Import Modal -->
<div id="import-modal" class="modal" style="display: none;">
    <div class="modal-backdrop"></div>
    <div class="modal-content" style="max-width: 700px;">
        <div class="modal-header">
            <h3>Импорт источников из CSV</h3>
            <button class="modal-close" id="close-import-modal">&times;</button>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label>CSV файл</label>
                <input type="file" id="csv-file" accept=".csv,.txt" class="input-field">
            </div>
            <div class="form-row" style="display: flex; gap: 16px;">
                <div class="form-group" style="flex: 1;">
                    <label>Разделитель</label>
                    <select id="csv-delimiter" class="input-field">
                        <option value=";">Точка с запятой (;)</option>
                        <option value=",">Запятая (,)</option>
                        <option value="\t">Табуляция</option>
                    </select>
                </div>
                <div class="form-group" style="flex: 1;">
                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                        <input type="checkbox" id="csv-skip-header" checked>
                        Пропустить заголовок
                    </label>
                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; margin-top: 8px;">
                        <input type="checkbox" id="csv-update-existing">
                        Обновлять существующие
                    </label>
                </div>
            </div>

            <div id="csv-preview" style="display: none; margin-top: 16px;">
                <h4 style="margin-bottom: 8px;">Предпросмотр (первые 5 записей)</h4>
                <div style="overflow-x: auto; max-height: 200px; border: 1px solid var(--border-color); border-radius: 8px;">
                    <table class="table" id="preview-table" style="margin: 0; font-size: 12px;"></table>
                </div>
                <p style="margin-top: 8px; color: var(--text-secondary);">
                    Найдено записей: <strong id="preview-count">0</strong>
                </p>
            </div>

            <div id="import-progress" style="display: none; margin-top: 16px;">
                <div style="background: var(--bg-tertiary); border-radius: 8px; height: 8px; overflow: hidden;">
                    <div id="progress-bar" style="background: var(--accent-primary); height: 100%; width: 0%; transition: width 0.3s;"></div>
                </div>
                <p style="text-align: center; margin-top: 8px; color: var(--text-secondary);" id="progress-text">Импортирование...</p>
            </div>

            <div id="import-result" style="display: none; margin-top: 16px; padding: 16px; background: var(--bg-tertiary); border-radius: 8px;"></div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" id="preview-csv-btn">Предпросмотр</button>
            <button class="btn btn-primary" id="start-import-btn" disabled>Импортировать</button>
        </div>
    </div>
</div>

<style>
.modal {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 1000;
    display: flex;
    align-items: center;
    justify-content: center;
}
.modal-backdrop {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
}
.modal-content {
    position: relative;
    background: var(--bg-primary);
    border-radius: 12px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
    max-height: 90vh;
    overflow-y: auto;
    width: 90%;
}
.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 24px;
    border-bottom: 1px solid var(--border-color);
}
.modal-header h3 {
    margin: 0;
    font-size: 18px;
}
.modal-close {
    background: none;
    border: none;
    font-size: 24px;
    cursor: pointer;
    color: var(--text-secondary);
    padding: 0;
    line-height: 1;
}
.modal-close:hover {
    color: var(--text-primary);
}
.modal-body {
    padding: 24px;
}
.modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    padding: 16px 24px;
    border-top: 1px solid var(--border-color);
}
.form-group {
    margin-bottom: 16px;
}
.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
    color: var(--text-secondary);
}
</style>

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

document.addEventListener('DOMContentLoaded', initSourcesCharts);

// CSV Import functionality
let parsedData = [];

document.getElementById('import-csv-btn').addEventListener('click', () => {
    document.getElementById('import-modal').style.display = 'flex';
    resetImportModal();
});

document.getElementById('close-import-modal').addEventListener('click', closeModal);
document.querySelector('.modal-backdrop').addEventListener('click', closeModal);

function closeModal() {
    document.getElementById('import-modal').style.display = 'none';
}

function resetImportModal() {
    document.getElementById('csv-file').value = '';
    document.getElementById('csv-preview').style.display = 'none';
    document.getElementById('import-progress').style.display = 'none';
    document.getElementById('import-result').style.display = 'none';
    document.getElementById('start-import-btn').disabled = true;
    parsedData = [];
}

document.getElementById('preview-csv-btn').addEventListener('click', async () => {
    const fileInput = document.getElementById('csv-file');
    if (!fileInput.files[0]) {
        alert('Пожалуйста, выберите CSV файл');
        return;
    }

    const delimiter = document.getElementById('csv-delimiter').value;
    const skipHeader = document.getElementById('csv-skip-header').checked;

    const text = await fileInput.files[0].text();
    const rows = parseCSV(text, delimiter === '\\t' ? '\t' : delimiter);

    if (skipHeader && rows.length > 0) {
        rows.shift();
    }

    // Map rows to source objects
    parsedData = rows.map(mapRowToSource).filter(s => s && s.domain);

    // Remove duplicates by domain (keep last)
    const uniqueMap = new Map();
    parsedData.forEach(s => uniqueMap.set(s.domain, s));
    parsedData = Array.from(uniqueMap.values());

    // Show preview
    const previewTable = document.getElementById('preview-table');
    const previewRows = parsedData.slice(0, 5);

    let html = '<thead><tr><th>Domain</th><th>URL Rating</th><th>Domain Rating</th><th>Experience</th><th>Expertise</th><th>Authority</th><th>Trust</th></tr></thead><tbody>';
    previewRows.forEach(s => {
        html += `<tr>
            <td>${escapeHtml(s.domain)}</td>
            <td>${s.url_rating || 0}</td>
            <td>${s.domain_rating || 0}</td>
            <td>${s.experience_score || 0}</td>
            <td>${s.expertise_score || 0}</td>
            <td>${s.authority_score || 0}</td>
            <td>${s.trust_score || 0}</td>
        </tr>`;
    });
    html += '</tbody>';
    previewTable.innerHTML = html;

    document.getElementById('preview-count').textContent = parsedData.length;
    document.getElementById('csv-preview').style.display = 'block';
    document.getElementById('start-import-btn').disabled = false;
});

document.getElementById('start-import-btn').addEventListener('click', async () => {
    if (parsedData.length === 0) return;

    const updateExisting = document.getElementById('csv-update-existing').checked;
    const progressBar = document.getElementById('progress-bar');
    const progressText = document.getElementById('progress-text');
    const resultDiv = document.getElementById('import-result');

    document.getElementById('csv-preview').style.display = 'none';
    document.getElementById('import-progress').style.display = 'block';
    document.getElementById('start-import-btn').disabled = true;

    let totalImported = 0;
    let totalUpdated = 0;
    let totalErrors = 0;

    // Process in batches of 50
    const batchSize = 50;
    const batches = [];
    for (let i = 0; i < parsedData.length; i += batchSize) {
        batches.push(parsedData.slice(i, i + batchSize));
    }

    for (let i = 0; i < batches.length; i++) {
        const progress = Math.round(((i + 1) / batches.length) * 100);
        progressBar.style.width = progress + '%';
        progressText.textContent = `Импортирование... ${progress}%`;

        try {
            const response = await fetch('/api/sources/import', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    sources: batches[i],
                    update_existing: updateExisting
                })
            });
            const result = await response.json();
            totalImported += result.imported || 0;
            totalUpdated += result.updated || 0;
            totalErrors += result.errors || 0;
        } catch (e) {
            totalErrors += batches[i].length;
        }
    }

    document.getElementById('import-progress').style.display = 'none';
    resultDiv.innerHTML = `
        <h4 style="margin: 0 0 12px 0; color: var(--success);">Импорт завершён</h4>
        <p style="margin: 4px 0;"><strong>Импортировано:</strong> ${totalImported}</p>
        <p style="margin: 4px 0;"><strong>Обновлено:</strong> ${totalUpdated}</p>
        <p style="margin: 4px 0;"><strong>Ошибок:</strong> ${totalErrors}</p>
        <p style="margin: 12px 0 0 0;"><a href="/sources" class="btn btn-primary" style="display: inline-block;">Обновить страницу</a></p>
    `;
    resultDiv.style.display = 'block';
});

function parseCSV(text, delimiter) {
    const lines = text.split(/\r?\n/).filter(line => line.trim());
    return lines.map(line => {
        const result = [];
        let current = '';
        let inQuotes = false;

        for (let i = 0; i < line.length; i++) {
            const char = line[i];
            if (char === '"') {
                inQuotes = !inQuotes;
            } else if (char === delimiter && !inQuotes) {
                result.push(current.trim());
                current = '';
            } else {
                current += char;
            }
        }
        result.push(current.trim());
        return result;
    });
}

// CSV format: Date;LLM;Prompt;Source;Domain;Affiliation;URL Rating;Domain Rating;Organic Traffic;Top Countries;Experience;Expertise;Authority;Trust;EEAT
function mapRowToSource(row) {
    if (row.length < 14) return null;

    const domain = extractDomain(row[4] || row[3]);
    if (!domain) return null;

    return {
        source_url: row[3] || '',
        domain: domain,
        country: row[5] || '',
        url_rating: parseFloat(row[6]) || 0,
        domain_rating: parseFloat(row[7]) || 0,
        organic_traffic: parseInt(row[8]) || 0,
        experience_score: parseFloat(row[10]) || 0,
        expertise_score: parseFloat(row[11]) || 0,
        authority_score: parseFloat(row[12]) || 0,
        trust_score: parseFloat(row[13]) || 0,
        type: 'media'
    };
}

function extractDomain(url) {
    if (!url) return null;
    try {
        if (!url.startsWith('http')) {
            url = 'https://' + url;
        }
        const parsed = new URL(url);
        return parsed.hostname.replace(/^www\./, '');
    } catch {
        // Try to extract domain directly
        const match = url.match(/([a-z0-9][-a-z0-9]*\.)+[a-z]{2,}/i);
        return match ? match[0].replace(/^www\./, '') : null;
    }
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
</script>
SCRIPTS;
?>
