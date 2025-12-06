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
                Экспорт CSV
            </a>
            <button class="btn btn-primary" id="add-source-btn">
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
<div class="filters-row" id="source-filters">
    <div class="filters-left">
        <div class="filter-select-group">
            <label class="filter-label">Тип:</label>
            <select class="filter-select" id="filter-type">
                <option value="all">Все типы</option>
                <option value="gov">Гос. сайты</option>
                <option value="media">СМИ</option>
                <option value="analytics">Аналитика</option>
                <option value="wiki">Wiki</option>
            </select>
        </div>
        <div class="filter-select-group">
            <label class="filter-label">Страна:</label>
            <select class="filter-select" id="filter-country">
                <option value="all">Все страны</option>
                <option value="KZ">Казахстан</option>
                <option value="RU">Россия</option>
                <option value="US">США</option>
                <option value="other">Другие</option>
            </select>
        </div>
        <div class="filter-select-group">
            <label class="filter-label">E-E-A-T:</label>
            <select class="filter-select" id="filter-eeat">
                <option value="all">Все уровни</option>
                <option value="high">Высокий (80+)</option>
                <option value="medium">Средний (50-79)</option>
                <option value="low">Низкий (&lt;50)</option>
            </select>
        </div>
    </div>
    <div class="filter-search">
        <input type="text" class="input-field" id="source-search" placeholder="Поиск по домену...">
    </div>
</div>

<!-- Sources Table -->
<div class="table-container sources-table-wrap">
    <table class="table" id="sources-table-wrapper">
        <thead>
            <tr>
                <th class="sortable" data-sort="domain">Домен <span class="sort-icon"></span></th>
                <th class="sortable" data-sort="type">Тип <span class="sort-icon"></span></th>
                <th class="sortable" data-sort="country">Страна <span class="sort-icon"></span></th>
                <th class="sortable" data-sort="expertise">Экспертиза <span class="sort-icon"></span></th>
                <th class="sortable" data-sort="experience">Опыт <span class="sort-icon"></span></th>
                <th class="sortable" data-sort="authority">Авторитет <span class="sort-icon"></span></th>
                <th class="sortable" data-sort="trust">Доверие <span class="sort-icon"></span></th>
                <th class="sortable" data-sort="eeat">E-E-A-T <span class="sort-icon"></span></th>
                <th class="sortable" data-sort="share">Доля % <span class="sort-icon"></span></th>
                <th>Автор</th>
                <th>HTTPS</th>
                <th class="no-sort">Действия</th>
            </tr>
        </thead>
        <tbody id="sources-table">
            <?php foreach ($sources as $index => $source): ?>
            <tr data-type="<?= $source['type'] ?>"
                data-domain="<?= strtolower($source['domain']) ?>"
                data-country="<?= $source['country'] ?>"
                data-expertise="<?= $source['expertise'] ?>"
                data-experience="<?= $source['experience'] ?>"
                data-authority="<?= $source['authority'] ?>"
                data-trust="<?= $source['trust'] ?>"
                data-eeat="<?= $source['eeat'] ?>"
                data-share="<?= $source['share'] ?>"
                data-index="<?= $index ?>">
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
                <td>
                    <div style="display: flex; gap: 4px;">
                        <button class="btn-icon edit-btn" data-index="<?= $index ?>" title="Редактировать">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                            </svg>
                        </button>
                        <button class="btn-icon delete-btn" data-index="<?= $index ?>" title="Удалить">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="3 6 5 6 21 6"/>
                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                            </svg>
                        </button>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div style="text-align: center; margin-top: 16px; color: var(--text-tertiary); font-size: 13px;" id="sources-count">
    Показано: <?= number_format($totalSources) ?> из <?= number_format($totalSources) ?>
</div>

<!-- Source Modal -->
<div class="modal-overlay" id="source-modal">
    <div class="modal">
        <h3 class="modal-title" id="modal-title">Добавить источник</h3>
        <form id="source-form">
            <input type="hidden" id="source-id">
            <div class="form-row">
                <div class="form-group" style="flex: 2;">
                    <label class="form-label">Домен *</label>
                    <input type="text" class="input-field" id="source-domain" required placeholder="example.com">
                </div>
                <div class="form-group" style="flex: 1;">
                    <label class="form-label">Тип</label>
                    <select class="select-field" id="source-type">
                        <option value="gov">Гос. сайт</option>
                        <option value="media" selected>СМИ</option>
                        <option value="analytics">Аналитика</option>
                        <option value="wiki">Wiki</option>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Страна</label>
                    <select class="select-field" id="source-country">
                        <option value="KZ">Казахстан</option>
                        <option value="RU">Россия</option>
                        <option value="US">США</option>
                        <option value="UK">Великобритания</option>
                        <option value="DE">Германия</option>
                        <option value="CN">Китай</option>
                        <option value="OTHER">Другая</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Доля %</label>
                    <input type="number" class="input-field" id="source-share" min="0" max="100" step="0.1" value="0">
                </div>
            </div>
            <div class="form-section-title">E-E-A-T оценки (0-100)</div>
            <div class="form-row eeat-row">
                <div class="form-group">
                    <label class="form-label">Экспертиза</label>
                    <input type="number" class="input-field" id="source-expertise" min="0" max="100" value="70">
                </div>
                <div class="form-group">
                    <label class="form-label">Опыт</label>
                    <input type="number" class="input-field" id="source-experience" min="0" max="100" value="70">
                </div>
                <div class="form-group">
                    <label class="form-label">Авторитет</label>
                    <input type="number" class="input-field" id="source-authority" min="0" max="100" value="70">
                </div>
                <div class="form-group">
                    <label class="form-label">Доверие</label>
                    <input type="number" class="input-field" id="source-trust" min="0" max="100" value="70">
                </div>
            </div>
            <div class="form-row checkbox-row">
                <label class="checkbox-label">
                    <input type="checkbox" id="source-author">
                    <span>Указан автор</span>
                </label>
                <label class="checkbox-label">
                    <input type="checkbox" id="source-https" checked>
                    <span>HTTPS</span>
                </label>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary" style="flex: 1;">
                    <span class="btn-text">Сохранить</span>
                </button>
                <button type="button" class="btn btn-secondary" id="cancel-modal">Отмена</button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal-overlay" id="delete-modal">
    <div class="modal" style="max-width: 400px;">
        <h3 class="modal-title">Удалить источник?</h3>
        <p style="color: var(--text-secondary); margin-bottom: 24px;">
            Вы уверены, что хотите удалить источник <strong id="delete-domain"></strong>? Это действие нельзя отменить.
        </p>
        <div class="form-actions">
            <button class="btn btn-danger" id="confirm-delete" style="flex: 1;">Удалить</button>
            <button class="btn btn-secondary" id="cancel-delete">Отмена</button>
        </div>
    </div>
</div>

<!-- Import CSV Modal -->
<div class="modal-overlay" id="import-modal">
    <div class="modal" style="max-width: 600px;">
        <h3 class="modal-title">Импорт источников из CSV</h3>
        <p style="color: var(--text-secondary); margin-bottom: 16px; font-size: 13px;">
            Формат: Date;LLM;Prompt;Source;Domain;Пренадлежность;URL Rating;Domain Rating;Organic/Traffic;Organic/Top Countries;Experience;Expertise;Authority;Trust;Итоговый EEAT
        </p>
        <form id="import-form">
            <div class="form-group" style="margin-bottom: 16px;">
                <label class="form-label">Выберите CSV файл</label>
                <input type="file" class="input-field" id="csv-file" accept=".csv,.txt" required style="padding: 12px;">
            </div>
            <div class="form-group" style="margin-bottom: 16px;">
                <label class="form-label">Разделитель</label>
                <select class="select-field" id="csv-delimiter">
                    <option value=";" selected>Точка с запятой (;)</option>
                    <option value=",">Запятая (,)</option>
                    <option value="\t">Табуляция</option>
                </select>
            </div>
            <div class="form-row checkbox-row" style="margin-bottom: 16px;">
                <label class="checkbox-label">
                    <input type="checkbox" id="skip-header" checked>
                    <span>Пропустить первую строку (заголовок)</span>
                </label>
                <label class="checkbox-label">
                    <input type="checkbox" id="update-existing" checked>
                    <span>Обновлять существующие домены</span>
                </label>
            </div>
            <div id="import-preview" style="display: none; margin-bottom: 16px;">
                <div class="form-label">Предпросмотр (первые 5 записей):</div>
                <div id="preview-content" style="max-height: 200px; overflow: auto; background: var(--bg-tertiary); border-radius: var(--radius-md); padding: 12px; font-size: 12px; font-family: monospace;"></div>
            </div>
            <div id="import-progress" style="display: none; margin-bottom: 16px;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                    <span>Импорт...</span>
                    <span id="progress-text">0 / 0</span>
                </div>
                <div style="background: var(--bg-tertiary); border-radius: 4px; height: 8px; overflow: hidden;">
                    <div id="progress-bar" style="background: var(--accent-primary); height: 100%; width: 0%; transition: width 0.3s;"></div>
                </div>
            </div>
            <div id="import-result" style="display: none; margin-bottom: 16px; padding: 12px; border-radius: var(--radius-md);"></div>
            <div class="form-actions">
                <button type="button" class="btn btn-secondary" id="preview-csv">Предпросмотр</button>
                <button type="submit" class="btn btn-primary" style="flex: 1;" id="start-import">
                    <span class="btn-text">Импортировать</span>
                </button>
                <button type="button" class="btn btn-secondary" id="cancel-import">Отмена</button>
            </div>
        </form>
    </div>
</div>

<!-- Toast -->
<div id="toast" class="toast"></div>

<style>
/* Fix page overflow - prevent horizontal scroll */
.content {
    overflow-x: hidden;
}

/* Responsive page header */
.page-header-top {
    display: flex;
    flex-wrap: wrap;
    gap: 16px;
    align-items: flex-start;
    justify-content: space-between;
    max-width: 100%;
}
.page-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    flex-shrink: 0;
}
@media (max-width: 900px) {
    .page-header-top {
        flex-direction: column;
        align-items: stretch;
    }
    .page-actions {
        width: 100%;
        justify-content: flex-start;
    }
    .page-actions .btn {
        flex: 0 1 auto;
    }
}
@media (max-width: 600px) {
    .page-actions .btn {
        flex: 1;
        min-width: 100px;
    }
    .page-actions .btn span:not(.btn-text) {
        display: none;
    }
}

/* Table wrapper - horizontal scroll only for table */
.sources-table-wrap {
    overflow-x: auto;
    max-width: 100%;
    -webkit-overflow-scrolling: touch;
}
.sources-table-wrap table {
    min-width: 1000px;
}

/* Filter row layout */
.filters-row {
    display: flex;
    flex-wrap: wrap;
    gap: 16px;
    align-items: center;
    justify-content: space-between;
}
.filters-left {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    align-items: center;
}
.filter-search {
    min-width: 200px;
    max-width: 300px;
}
.filter-search .input-field {
    width: 100%;
}
@media (max-width: 900px) {
    .filters-row {
        flex-direction: column;
        align-items: stretch;
    }
    .filters-left {
        width: 100%;
    }
    .filter-search {
        width: 100%;
        max-width: none;
    }
}

/* Filter select styles */
.filter-select-group {
    display: flex;
    align-items: center;
    gap: 8px;
}
.filter-label {
    font-size: 13px;
    color: var(--text-tertiary);
    font-weight: 500;
    white-space: nowrap;
}
.filter-select {
    background: var(--bg-tertiary);
    border: 1px solid var(--border-subtle);
    border-radius: var(--radius-md);
    padding: 8px 12px;
    color: var(--text-primary);
    font-size: 13px;
    cursor: pointer;
    min-width: 130px;
    transition: all var(--transition-fast);
}
.filter-select:hover {
    border-color: var(--border-medium);
}
.filter-select:focus {
    outline: none;
    border-color: var(--accent-primary);
    box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.2);
}
@media (max-width: 600px) {
    .filter-select-group {
        flex: 1;
        min-width: 140px;
    }
    .filter-select {
        flex: 1;
        min-width: 0;
    }
}

/* Sortable headers */
.sortable {
    cursor: pointer;
    user-select: none;
    white-space: nowrap;
}
.sortable:hover {
    background: var(--bg-card-hover);
}
.sort-icon {
    display: inline-block;
    margin-left: 4px;
    opacity: 0.3;
    font-size: 10px;
}
.sort-icon::after {
    content: '⇅';
}
.sortable.sort-asc .sort-icon {
    opacity: 1;
}
.sortable.sort-asc .sort-icon::after {
    content: '↑';
}
.sortable.sort-desc .sort-icon {
    opacity: 1;
}
.sortable.sort-desc .sort-icon::after {
    content: '↓';
}

.btn-icon {
    width: 32px;
    height: 32px;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    background: transparent;
    border: 1px solid var(--border-subtle);
    border-radius: var(--radius-sm);
    cursor: pointer;
    transition: all var(--transition-fast);
}
.btn-icon svg {
    width: 14px;
    height: 14px;
    stroke: var(--text-tertiary);
}
.btn-icon:hover {
    background: var(--bg-card-hover);
    border-color: var(--border-medium);
}
.btn-icon:hover svg {
    stroke: var(--text-primary);
}
.btn-icon.delete-btn:hover {
    border-color: var(--danger);
}
.btn-icon.delete-btn:hover svg {
    stroke: var(--danger);
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
}
.modal-overlay.show { display: flex; }
.modal {
    background: var(--bg-card);
    border-radius: var(--radius-xl);
    padding: 28px;
    max-width: 550px;
    width: 90%;
}
.modal-title {
    font-size: 20px;
    font-weight: 600;
    margin-bottom: 24px;
    color: var(--text-primary);
}

.form-row {
    display: flex;
    gap: 16px;
    margin-bottom: 16px;
}
.form-row .form-group {
    flex: 1;
}
.form-section-title {
    font-size: 13px;
    font-weight: 600;
    color: var(--text-tertiary);
    margin-bottom: 12px;
    margin-top: 8px;
}
.eeat-row .form-group {
    flex: 1;
}
.checkbox-row {
    display: flex;
    gap: 24px;
    margin-bottom: 20px;
}
.checkbox-label {
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    font-size: 14px;
    color: var(--text-secondary);
}
.checkbox-label input {
    width: 18px;
    height: 18px;
    accent-color: var(--accent-primary);
}
.form-actions {
    display: flex;
    gap: 12px;
    margin-top: 24px;
}

.btn-danger {
    background: var(--danger);
    color: white;
    border: none;
}
.btn-danger:hover {
    background: #dc2626;
}

/* Toast */
.toast {
    position: fixed;
    bottom: 24px;
    right: 24px;
    padding: 16px 24px;
    border-radius: var(--radius-md);
    font-size: 14px;
    font-weight: 500;
    z-index: 1001;
    transform: translateY(100px);
    opacity: 0;
    transition: all 0.3s ease;
}
.toast.show { transform: translateY(0); opacity: 1; }
.toast.success { background: var(--success); color: white; }
.toast.error { background: var(--danger); color: white; }
</style>

<!-- Store sources data for editing -->
<script type="application/json" id="sources-data">
<?= json_encode(array_map(function($s) {
    return [
        'id' => $s['id'],
        'domain' => $s['domain'],
        'type' => $s['type'],
        'country' => $s['country'],
        'expertise' => $s['expertise'],
        'experience' => $s['experience'],
        'authority' => $s['authority'],
        'trust' => $s['trust'],
        'share' => $s['share'],
        'author' => $s['author'],
        'https' => $s['https']
    ];
}, $sources), JSON_UNESCAPED_UNICODE) ?>
</script>

<?php
$statsJson = json_encode($stats);
?>
<script id="chart-stats-data" type="application/json"><?= $statsJson ?></script>
<?php
$pageScripts = <<<'SCRIPTS'
<script>
const chartStats = JSON.parse(document.getElementById('chart-stats-data').textContent);

function initSourcesCharts() {
    const themeColors = getChartColors();
    updateChartDefaults();

    const colors = {
        primary: '#6366f1',
        success: '#22c55e',
        warning: '#f59e0b',
        info: '#3b82f6',
        purple: '#8b5cf6',
        danger: '#ef4444',
        cyan: '#06b6d4'
    };

    // E-E-A-T Radar Chart - using real averages
    const eeatData = [
        chartStats.avgExpertise || 0,
        chartStats.avgExperience || 0,
        chartStats.avgAuthority || 0,
        chartStats.avgTrust || 0
    ];

    pageCharts.eeat = new Chart(document.getElementById('eeatRadarChart'), {
        type: 'radar',
        data: {
            labels: ['Экспертиза', 'Опыт', 'Авторитетность', 'Надёжность'],
            datasets: [{
                data: eeatData,
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

    // Geography Pie Chart - using real country stats
    const countryStats = chartStats.countryStats || {};
    const countryLabels = [];
    const countryData = [];
    const countryColors = [];
    const countryColorMap = {
        'KZ': colors.primary,
        'RU': colors.success,
        'US': colors.warning,
        'UK': colors.info,
        'DE': colors.purple,
        'CN': colors.danger,
        'OTHER': colors.cyan
    };

    Object.entries(countryStats).forEach(([country, count]) => {
        const labelMap = { 'KZ': 'Казахстан', 'RU': 'Россия', 'US': 'США', 'UK': 'UK', 'DE': 'Германия', 'CN': 'Китай', 'OTHER': 'Другие' };
        countryLabels.push(labelMap[country] || country);
        countryData.push(count);
        countryColors.push(countryColorMap[country] || colors.cyan);
    });

    pageCharts.geo = new Chart(document.getElementById('geoPieChart'), {
        type: 'doughnut',
        data: {
            labels: countryLabels.length > 0 ? countryLabels : ['Нет данных'],
            datasets: [{
                data: countryData.length > 0 ? countryData : [1],
                backgroundColor: countryColors.length > 0 ? countryColors : [colors.primary],
                borderWidth: 0
            }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, cutout: '65%' }
    });

    // Type Pie Chart - using real type stats
    const typeStats = chartStats.typeStats || {};
    const typeLabels = [];
    const typeData = [];
    const typeColors = [];
    const typeColorMap = {
        'gov': colors.primary,
        'media': colors.info,
        'analytics': colors.purple,
        'wiki': colors.warning
    };
    const typeLabelMap = { 'gov': 'Гос. сайты', 'media': 'СМИ', 'analytics': 'Аналитика', 'wiki': 'Wiki' };

    Object.entries(typeStats).forEach(([type, count]) => {
        typeLabels.push(typeLabelMap[type] || type);
        typeData.push(count);
        typeColors.push(typeColorMap[type] || colors.cyan);
    });

    pageCharts.type = new Chart(document.getElementById('typePieChart'), {
        type: 'doughnut',
        data: {
            labels: typeLabels.length > 0 ? typeLabels : ['Нет данных'],
            datasets: [{
                data: typeData.length > 0 ? typeData : [1],
                backgroundColor: typeColors.length > 0 ? typeColors : [colors.primary],
                borderWidth: 0
            }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, cutout: '65%' }
    });

    // Update legend dynamically
    updateGeoLegend(countryStats);
    updateTypeLegend(typeStats);
}

function updateGeoLegend(countryStats) {
    const total = Object.values(countryStats).reduce((a, b) => a + b, 0) || 1;
    const labelMap = { 'KZ': 'Казахстан', 'RU': 'Россия', 'US': 'США', 'UK': 'UK', 'DE': 'Германия', 'CN': 'Китай', 'OTHER': 'Другие' };
    const colorMap = { 'KZ': '#6366f1', 'RU': '#22c55e', 'US': '#f59e0b', 'UK': '#3b82f6', 'DE': '#8b5cf6', 'CN': '#ef4444', 'OTHER': '#06b6d4' };

    const legendContainer = document.querySelector('.source-card:nth-child(2) .legend');
    if (legendContainer && Object.keys(countryStats).length > 0) {
        legendContainer.innerHTML = Object.entries(countryStats)
            .sort((a, b) => b[1] - a[1])
            .slice(0, 5)
            .map(([country, count]) => {
                const pct = Math.round((count / total) * 100);
                return '<div class="legend-item"><span class="legend-dot" style="background: ' + (colorMap[country] || '#06b6d4') + ';"></span><span>' + pct + '% — ' + (labelMap[country] || country) + '</span></div>';
            }).join('');
    }
}

function updateTypeLegend(typeStats) {
    const labelMap = { 'gov': 'Гос. сайты', 'media': 'СМИ', 'analytics': 'Аналитика', 'wiki': 'Wiki' };
    const colorMap = { 'gov': '#6366f1', 'media': '#3b82f6', 'analytics': '#8b5cf6', 'wiki': '#f59e0b' };

    const legendContainer = document.querySelector('.source-card:nth-child(3) .legend');
    if (legendContainer && Object.keys(typeStats).length > 0) {
        legendContainer.innerHTML = Object.entries(typeStats)
            .sort((a, b) => b[1] - a[1])
            .map(([type, count]) => {
                return '<div class="legend-item"><span class="legend-dot" style="background: ' + (colorMap[type] || '#06b6d4') + ';"></span><span>' + (labelMap[type] || type) + '</span></div>';
            }).join('');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    initSourcesCharts();

    // Load sources data
    const sourcesData = JSON.parse(document.getElementById('sources-data').textContent);

    // Toast helper
    function showToast(message, type = 'success') {
        const toast = document.getElementById('toast');
        toast.textContent = message;
        toast.className = 'toast ' + type + ' show';
        setTimeout(() => toast.classList.remove('show'), 3000);
    }

    // Modal elements
    const modal = document.getElementById('source-modal');
    const deleteModal = document.getElementById('delete-modal');
    const form = document.getElementById('source-form');
    let editingId = null;
    let deletingId = null;

    // Open add modal
    document.getElementById('add-source-btn').addEventListener('click', () => {
        editingId = null;
        document.getElementById('modal-title').textContent = 'Добавить источник';
        form.reset();
        document.getElementById('source-https').checked = true;
        modal.classList.add('show');
    });

    // Edit buttons
    document.querySelectorAll('.edit-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            const index = parseInt(this.dataset.index);
            const data = sourcesData[index];
            if (!data) return;

            editingId = data.id;
            document.getElementById('modal-title').textContent = 'Редактировать источник';
            document.getElementById('source-id').value = data.id;
            document.getElementById('source-domain').value = data.domain;
            document.getElementById('source-type').value = data.type;
            document.getElementById('source-country').value = data.country;
            document.getElementById('source-share').value = data.share;
            document.getElementById('source-expertise').value = data.expertise;
            document.getElementById('source-experience').value = data.experience;
            document.getElementById('source-authority').value = data.authority;
            document.getElementById('source-trust').value = data.trust;
            document.getElementById('source-author').checked = data.author;
            document.getElementById('source-https').checked = data.https;
            modal.classList.add('show');
        });
    });

    // Delete buttons
    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            const index = parseInt(this.dataset.index);
            const data = sourcesData[index];
            if (!data) return;

            deletingId = data.id;
            document.getElementById('delete-domain').textContent = data.domain;
            deleteModal.classList.add('show');
        });
    });

    // Confirm delete
    document.getElementById('confirm-delete').addEventListener('click', async () => {
        if (!deletingId) return;

        try {
            const resp = await fetch(`/api/sources/${deletingId}`, { method: 'DELETE' });
            const result = await resp.json();

            if (result.success) {
                showToast('Источник удалён', 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                showToast(result.error || 'Ошибка удаления', 'error');
            }
        } catch (err) {
            showToast('Ошибка сети', 'error');
        }

        deleteModal.classList.remove('show');
    });

    // Form submit
    form.addEventListener('submit', async function(e) {
        e.preventDefault();

        const data = {
            domain: document.getElementById('source-domain').value,
            type: document.getElementById('source-type').value,
            country: document.getElementById('source-country').value,
            share_percent: parseFloat(document.getElementById('source-share').value) || 0,
            expertise_score: parseInt(document.getElementById('source-expertise').value) || 0,
            experience_score: parseInt(document.getElementById('source-experience').value) || 0,
            authority_score: parseInt(document.getElementById('source-authority').value) || 0,
            trust_score: parseInt(document.getElementById('source-trust').value) || 0,
            has_author: document.getElementById('source-author').checked,
            has_https: document.getElementById('source-https').checked
        };

        const url = editingId ? `/api/sources/${editingId}` : '/api/sources';
        const method = editingId ? 'PUT' : 'POST';

        try {
            const resp = await fetch(url, {
                method,
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });

            const result = await resp.json();

            if (result.success) {
                showToast(editingId ? 'Источник обновлён' : 'Источник добавлен', 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                showToast(result.error || 'Ошибка сохранения', 'error');
            }
        } catch (err) {
            showToast('Ошибка сети', 'error');
        }

        modal.classList.remove('show');
    });

    // Close modals
    document.getElementById('cancel-modal').addEventListener('click', () => modal.classList.remove('show'));
    document.getElementById('cancel-delete').addEventListener('click', () => deleteModal.classList.remove('show'));

    document.querySelectorAll('.modal-overlay').forEach(m => {
        m.addEventListener('click', function(e) {
            if (e.target === this) this.classList.remove('show');
        });
    });

    // Import CSV functionality
    const importModal = document.getElementById('import-modal');
    const csvFileInput = document.getElementById('csv-file');
    let parsedData = [];

    document.getElementById('import-csv-btn').addEventListener('click', () => {
        parsedData = [];
        document.getElementById('import-form').reset();
        document.getElementById('import-preview').style.display = 'none';
        document.getElementById('import-progress').style.display = 'none';
        document.getElementById('import-result').style.display = 'none';
        importModal.classList.add('show');
    });

    document.getElementById('cancel-import').addEventListener('click', () => {
        importModal.classList.remove('show');
    });

    // Parse CSV
    function parseCSV(text, delimiter) {
        const lines = text.split(/\r?\n/).filter(line => line.trim());
        return lines.map(line => {
            // Handle quoted fields
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

    // Map CSV row to source data
    // Format: Date;LLM;Prompt;Source;Domain;Пренадлежность;URL Rating;Domain Rating;Organic/Traffic;Organic/Top Countries;Experience;Expertise;Authority;Trust;Итоговый EEAT
    function mapRowToSource(row) {
        if (row.length < 15) return null;

        const domain = row[4]; // Domain column
        if (!domain || domain === 'Domain') return null;

        const affiliation = row[5]; // Пренадлежность
        let country = 'OTHER';
        let type = 'media';

        if (affiliation === 'International') {
            country = 'US';
            type = 'media';
        } else if (affiliation === 'KZ' || affiliation.includes('Казахстан')) {
            country = 'KZ';
        } else if (affiliation === 'RU' || affiliation.includes('Росси')) {
            country = 'RU';
        }

        // Determine type from domain
        if (domain.includes('.gov') || domain.includes('.kz') && domain.includes('gov')) {
            type = 'gov';
        } else if (domain.includes('wiki')) {
            type = 'wiki';
        } else if (domain.includes('analytics') || domain.includes('research')) {
            type = 'analytics';
        }

        return {
            domain: domain,
            source_url: row[3], // Source URL
            type: type,
            country: country,
            url_rating: parseFloat(row[6]) || 0,
            domain_rating: parseFloat(row[7]) || 0,
            organic_traffic: parseInt(row[8]) || 0,
            experience_score: parseInt(row[10]) || 0,
            expertise_score: parseInt(row[11]) || 0,
            authority_score: parseInt(row[12]) || 0,
            trust_score: parseInt(row[13]) || 0,
            eeat_combined: parseInt(row[14]) || 0,
            has_https: row[3] ? row[3].startsWith('https') : true,
            has_author: false,
            share_percent: 0
        };
    }

    // Preview CSV
    document.getElementById('preview-csv').addEventListener('click', () => {
        const file = csvFileInput.files[0];
        if (!file) {
            showToast('Выберите файл', 'error');
            return;
        }

        const delimiter = document.getElementById('csv-delimiter').value;
        const skipHeader = document.getElementById('skip-header').checked;

        const reader = new FileReader();
        reader.onload = function(e) {
            const text = e.target.result;
            const rows = parseCSV(text, delimiter);

            const startIndex = skipHeader ? 1 : 0;
            parsedData = [];

            for (let i = startIndex; i < rows.length; i++) {
                const source = mapRowToSource(rows[i]);
                if (source) {
                    parsedData.push(source);
                }
            }

            // Remove duplicates by domain (keep last occurrence)
            const uniqueMap = new Map();
            parsedData.forEach(s => uniqueMap.set(s.domain, s));
            parsedData = Array.from(uniqueMap.values());

            // Show preview
            const previewContent = document.getElementById('preview-content');
            const previewItems = parsedData.slice(0, 5).map(s =>
                `<div style="margin-bottom: 8px; padding-bottom: 8px; border-bottom: 1px solid var(--border-subtle);">
                    <strong>${s.domain}</strong> (${s.country}, ${s.type})<br>
                    E-E-A-T: ${s.eeat_combined} | Exp: ${s.experience_score} | Auth: ${s.authority_score} | Trust: ${s.trust_score}
                </div>`
            ).join('');

            previewContent.innerHTML = previewItems +
                `<div style="color: var(--text-tertiary); margin-top: 8px;">
                    Всего уникальных доменов: <strong>${parsedData.length}</strong>
                </div>`;

            document.getElementById('import-preview').style.display = 'block';
        };
        reader.readAsText(file, 'UTF-8');
    });

    // Import form submit
    document.getElementById('import-form').addEventListener('submit', async function(e) {
        e.preventDefault();

        if (parsedData.length === 0) {
            showToast('Сначала сделайте предпросмотр', 'error');
            return;
        }

        const updateExisting = document.getElementById('update-existing').checked;
        const progressDiv = document.getElementById('import-progress');
        const progressBar = document.getElementById('progress-bar');
        const progressText = document.getElementById('progress-text');
        const resultDiv = document.getElementById('import-result');

        progressDiv.style.display = 'block';
        resultDiv.style.display = 'none';

        let imported = 0;
        let updated = 0;
        let errors = 0;
        let errorDetails = [];
        const total = parsedData.length;
        const batchSize = 50;

        // Send in batches
        for (let i = 0; i < total; i += batchSize) {
            const batch = parsedData.slice(i, i + batchSize);

            try {
                const resp = await fetch('/api/sources/import', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        sources: batch,
                        update_existing: updateExisting
                    })
                });

                const result = await resp.json();
                console.log('Batch result:', result);
                if (result.success) {
                    imported += result.imported || 0;
                    updated += result.updated || 0;
                    errors += result.errors || 0;
                    if (result.error_details) {
                        errorDetails = errorDetails.concat(result.error_details);
                    }
                } else {
                    errors += batch.length;
                    errorDetails.push(result.error || 'Unknown error');
                }
            } catch (err) {
                errors += batch.length;
                errorDetails.push(err.message);
            }

            const progress = Math.min(100, Math.round(((i + batch.length) / total) * 100));
            progressBar.style.width = progress + '%';
            progressText.textContent = `${i + batch.length} / ${total}`;
        }

        progressDiv.style.display = 'none';
        resultDiv.style.display = 'block';

        if (imported > 0 || updated > 0) {
            resultDiv.style.background = 'rgba(34, 197, 94, 0.1)';
            resultDiv.style.color = 'var(--success)';
            resultDiv.innerHTML = `Импорт завершён! Добавлено: ${imported}, обновлено: ${updated}` + (errors > 0 ? `, ошибок: ${errors}` : '');
            setTimeout(() => location.reload(), 2000);
        } else if (errors > 0) {
            resultDiv.style.background = 'rgba(239, 68, 68, 0.1)';
            resultDiv.style.color = 'var(--danger)';
            resultDiv.innerHTML = `Ошибок: ${errors}. Проверьте консоль браузера для деталей.`;
            console.error('Import errors:', errorDetails);
        } else {
            resultDiv.style.background = 'rgba(245, 158, 11, 0.1)';
            resultDiv.style.color = 'var(--warning)';
            resultDiv.innerHTML = `Все домены уже существуют в базе. Ничего не импортировано.`;
        }
    });

    // Filter functionality
    const searchInput = document.getElementById('source-search');
    const tableBody = document.getElementById('sources-table');
    const rows = Array.from(tableBody.querySelectorAll('tr'));

    // Filter selects
    const filterType = document.getElementById('filter-type');
    const filterCountry = document.getElementById('filter-country');
    const filterEeat = document.getElementById('filter-eeat');

    // Sort state
    let sortColumn = null;
    let sortDirection = 'asc';

    // Filter select handlers
    filterType.addEventListener('change', applyFilters);
    filterCountry.addEventListener('change', applyFilters);
    filterEeat.addEventListener('change', applyFilters);
    searchInput.addEventListener('input', applyFilters);

    function applyFilters() {
        const searchTerm = searchInput.value.toLowerCase();
        const typeValue = filterType.value;
        const countryValue = filterCountry.value;
        const eeatValue = filterEeat.value;

        rows.forEach(row => {
            // Type filter
            const matchesType = typeValue === 'all' || row.dataset.type === typeValue;

            // Country filter
            let matchesCountry = countryValue === 'all';
            if (!matchesCountry) {
                if (countryValue === 'other') {
                    matchesCountry = !['KZ', 'RU', 'US'].includes(row.dataset.country);
                } else {
                    matchesCountry = row.dataset.country === countryValue;
                }
            }

            // E-E-A-T filter
            let matchesEeat = eeatValue === 'all';
            if (!matchesEeat) {
                const eeat = parseInt(row.dataset.eeat) || 0;
                if (eeatValue === 'high') matchesEeat = eeat >= 80;
                else if (eeatValue === 'medium') matchesEeat = eeat >= 50 && eeat < 80;
                else if (eeatValue === 'low') matchesEeat = eeat < 50;
            }

            // Search filter
            const matchesSearch = !searchTerm || row.dataset.domain.includes(searchTerm);

            row.style.display = matchesType && matchesCountry && matchesEeat && matchesSearch ? '' : 'none';
        });

        updateVisibleCount();
    }

    function updateVisibleCount() {
        const visible = rows.filter(r => r.style.display !== 'none').length;
        const countEl = document.getElementById('sources-count');
        if (countEl) {
            countEl.textContent = `Показано: ${visible} из ${rows.length}`;
        }
    }

    // Sorting functionality
    document.querySelectorAll('.sortable').forEach(th => {
        th.addEventListener('click', function() {
            const column = this.dataset.sort;
            if (!column) return;

            // Toggle direction if same column
            if (sortColumn === column) {
                sortDirection = sortDirection === 'asc' ? 'desc' : 'asc';
            } else {
                sortColumn = column;
                sortDirection = 'asc';
            }

            // Update header classes
            document.querySelectorAll('.sortable').forEach(h => {
                h.classList.remove('sort-asc', 'sort-desc');
            });
            this.classList.add(sortDirection === 'asc' ? 'sort-asc' : 'sort-desc');

            // Sort rows
            const sortedRows = [...rows].sort((a, b) => {
                let aVal = a.dataset[column] || '';
                let bVal = b.dataset[column] || '';

                // Numeric columns
                const numericCols = ['expertise', 'experience', 'authority', 'trust', 'eeat', 'share'];
                if (numericCols.includes(column)) {
                    aVal = parseFloat(aVal) || 0;
                    bVal = parseFloat(bVal) || 0;
                    return sortDirection === 'asc' ? aVal - bVal : bVal - aVal;
                }

                // String columns
                aVal = aVal.toString().toLowerCase();
                bVal = bVal.toString().toLowerCase();
                if (sortDirection === 'asc') {
                    return aVal.localeCompare(bVal);
                } else {
                    return bVal.localeCompare(aVal);
                }
            });

            // Reorder DOM
            sortedRows.forEach(row => tableBody.appendChild(row));
            applyFilters();
        });
    });

    // Initial count
    updateVisibleCount();
});
</script>
SCRIPTS;
?>
