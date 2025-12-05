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
                <th class="no-sort">Действия</th>
            </tr>
        </thead>
        <tbody id="sources-table">
            <?php foreach ($sources as $index => $source): ?>
            <tr data-type="<?= $source['type'] ?>" data-domain="<?= strtolower($source['domain']) ?>" data-index="<?= $index ?>">
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

<div style="text-align: center; margin-top: 16px; color: var(--text-tertiary); font-size: 13px;">
    Всего источников: <?= number_format($totalSources) ?>
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

<!-- Toast -->
<div id="toast" class="toast"></div>

<style>
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
