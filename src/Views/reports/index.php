<div class="page-header animate-on-scroll">
    <div class="page-header-top">
        <div class="page-title-group">
            <h1 class="page-title">Отчёты</h1>
            <p class="page-subtitle">Генерация и управление аналитическими отчётами</p>
        </div>
    </div>
</div>

<!-- Report Types Grid -->
<h2 class="section-title">Типы отчётов</h2>
<div class="reports-grid stagger-children">
    <?php foreach ($reportTypes as $report): ?>
    <div class="report-card" data-report-type="<?= $report['id'] ?>">
        <div class="report-icon"><?= $report['icon'] ?></div>
        <h3 class="report-title"><?= htmlspecialchars($report['title']) ?></h3>
        <p class="report-description"><?= htmlspecialchars($report['description']) ?></p>
        <?php if ($report['lastGenerated']): ?>
        <div class="report-meta">
            Последний: <?= date('d.m.Y', strtotime($report['lastGenerated'])) ?>
        </div>
        <?php endif; ?>
        <button class="btn btn-primary btn-sm generate-report-btn" data-type="<?= $report['id'] ?>" style="width: 100%; margin-top: 16px;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 16px; height: 16px;">
                <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/>
                <polyline points="14,2 14,8 20,8"/>
            </svg>
            <span class="btn-text">Сгенерировать</span>
        </button>
    </div>
    <?php endforeach; ?>
</div>

<!-- Recent Reports -->
<h2 class="section-title" style="margin-top: 48px;">Последние отчёты</h2>
<div class="table-container">
    <table class="table">
        <thead>
            <tr>
                <th>Название отчёта</th>
                <th>Тип</th>
                <th>Дата создания</th>
                <th>Размер</th>
                <th class="no-sort">Действия</th>
            </tr>
        </thead>
        <tbody id="reports-table">
            <?php if (empty($recentReports)): ?>
            <tr class="empty-row">
                <td colspan="5" style="text-align: center; color: var(--text-tertiary); padding: 40px;">
                    Нет сгенерированных отчётов
                </td>
            </tr>
            <?php else: ?>
            <?php foreach ($recentReports as $report): ?>
            <tr data-report-id="<?= $report['id'] ?>">
                <td>
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <span style="font-size: 20px;">📄</span>
                        <span><?= htmlspecialchars($report['name']) ?></span>
                    </div>
                </td>
                <td>
                    <span class="type-badge <?= $report['type'] ?>"><?= ucfirst($report['type']) ?></span>
                </td>
                <td style="color: var(--text-tertiary);">
                    <?= date('d.m.Y', strtotime($report['date'])) ?>
                </td>
                <td style="font-family: 'JetBrains Mono', monospace; color: var(--text-tertiary);">
                    <?= $report['size'] ?>
                </td>
                <td>
                    <div style="display: flex; gap: 8px;">
                        <button class="btn btn-secondary btn-sm download-btn" data-id="<?= $report['id'] ?>" data-format="csv">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 14px; height: 14px;">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                <polyline points="7,10 12,15 17,10"/>
                                <line x1="12" y1="15" x2="12" y2="3"/>
                            </svg>
                            CSV
                        </button>
                        <button class="btn btn-secondary btn-sm download-btn" data-id="<?= $report['id'] ?>" data-format="pdf">
                            PDF
                        </button>
                        <button class="btn btn-secondary btn-sm view-btn" data-id="<?= $report['id'] ?>">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 14px; height: 14px;">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                            Просмотр
                        </button>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Project Select Modal (for project reports) -->
<div class="modal-overlay" id="project-modal">
    <div class="modal">
        <h3 class="modal-title">Выберите проект</h3>
        <form id="project-report-form">
            <div class="form-group">
                <label class="form-label">Проект</label>
                <select class="select-field" id="project-select" required>
                    <option value="">-- Выберите проект --</option>
                    <?php foreach ($projects as $p): ?>
                    <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div style="display: flex; gap: 12px; margin-top: 24px;">
                <button type="submit" class="btn btn-primary" style="flex: 1;">Сгенерировать</button>
                <button type="button" class="btn btn-secondary" id="cancel-project-modal">Отмена</button>
            </div>
        </form>
    </div>
</div>

<!-- Report Preview Modal -->
<div class="modal-overlay" id="preview-modal">
    <div class="modal" style="max-width: 800px; max-height: 80vh; overflow: hidden; display: flex; flex-direction: column;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <h3 class="modal-title" style="margin-bottom: 0;" id="preview-title">Просмотр отчёта</h3>
            <button class="btn btn-secondary btn-sm" id="close-preview">&times;</button>
        </div>
        <div id="preview-content" style="flex: 1; overflow-y: auto; background: var(--bg-secondary); border-radius: var(--radius-md); padding: 20px;">
            <pre style="white-space: pre-wrap; font-family: 'JetBrains Mono', monospace; font-size: 13px; margin: 0;"></pre>
        </div>
    </div>
</div>

<!-- Toast -->
<div id="toast" class="toast"></div>

<style>
.reports-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 24px;
    margin-bottom: 32px;
}

.report-card {
    background: var(--bg-card);
    border: 1px solid var(--border-subtle);
    border-radius: var(--radius-xl);
    padding: 24px;
    transition: all var(--transition-base);
}

.report-card:hover {
    border-color: var(--border-medium);
    transform: translateY(-2px);
}

.report-card.generating {
    opacity: 0.7;
    pointer-events: none;
}

.report-icon {
    font-size: 36px;
    margin-bottom: 16px;
}

.report-title {
    font-size: 16px;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 8px;
}

.report-description {
    font-size: 13px;
    color: var(--text-tertiary);
    line-height: 1.5;
    margin-bottom: 12px;
}

.report-meta {
    font-size: 12px;
    color: var(--text-muted);
}

.btn-sm {
    padding: 8px 14px;
    font-size: 13px;
}

.type-badge {
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
}
.type-badge.weekly { background: rgba(99, 102, 241, 0.15); color: var(--accent-primary); }
.type-badge.monthly { background: rgba(34, 197, 94, 0.15); color: var(--success); }
.type-badge.llm { background: rgba(249, 115, 22, 0.15); color: #f97316; }
.type-badge.project { background: rgba(139, 92, 246, 0.15); color: #8b5cf6; }

/* Modal */
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
    padding: 32px;
    max-width: 500px;
    width: 90%;
}
.modal-title { font-size: 20px; font-weight: 600; margin-bottom: 24px; color: var(--text-primary); }

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
.toast.info { background: var(--accent-primary); color: white; }

/* Spinner */
.spinner {
    display: inline-block;
    width: 14px;
    height: 14px;
    border: 2px solid rgba(255,255,255,0.3);
    border-radius: 50%;
    border-top-color: white;
    animation: spin 0.8s linear infinite;
}
@keyframes spin {
    to { transform: rotate(360deg); }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toast helper
    function showToast(message, type = 'success') {
        const toast = document.getElementById('toast');
        toast.textContent = message;
        toast.className = 'toast ' + type + ' show';
        setTimeout(() => toast.classList.remove('show'), 3000);
    }

    // Generate report buttons
    document.querySelectorAll('.generate-report-btn').forEach(btn => {
        btn.addEventListener('click', async function() {
            const type = this.dataset.type;

            // For project reports, show project selector
            if (type === 'project') {
                document.getElementById('project-modal').classList.add('show');
                return;
            }

            await generateReport(type, null, this);
        });
    });

    // Project report form
    document.getElementById('project-report-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        const projectId = document.getElementById('project-select').value;
        if (!projectId) {
            showToast('Выберите проект', 'error');
            return;
        }
        document.getElementById('project-modal').classList.remove('show');
        const btn = document.querySelector('.generate-report-btn[data-type="project"]');
        await generateReport('project', projectId, btn);
    });

    // Generate report function
    async function generateReport(type, projectId, btn) {
        const card = btn.closest('.report-card');
        const btnText = btn.querySelector('.btn-text');
        const originalText = btnText.textContent;

        card.classList.add('generating');
        btnText.innerHTML = '<span class="spinner"></span> Генерация...';

        try {
            const resp = await fetch('/api/reports', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ type, project_id: projectId })
            });

            const result = await resp.json();

            if (result.success) {
                showToast('Отчёт сгенерирован', 'success');
                // Reload to show new report in table
                setTimeout(() => location.reload(), 1000);
            } else {
                showToast(result.error || 'Ошибка генерации', 'error');
            }
        } catch (err) {
            showToast('Ошибка сети', 'error');
        } finally {
            card.classList.remove('generating');
            btnText.textContent = originalText;
        }
    }

    // Download buttons
    document.querySelectorAll('.download-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            window.open('https://url.reportview.kz/table/', '_blank');
        });
    });

    // View buttons
    document.querySelectorAll('.view-btn').forEach(btn => {
        btn.addEventListener('click', async function() {
            const id = this.dataset.id;

            try {
                const resp = await fetch(`/api/reports/export/${id}?format=json`);
                const data = await resp.json();

                document.getElementById('preview-title').textContent = data.report?.title || 'Просмотр отчёта';
                document.querySelector('#preview-content pre').textContent = JSON.stringify(data, null, 2);
                document.getElementById('preview-modal').classList.add('show');
            } catch (err) {
                showToast('Ошибка загрузки отчёта', 'error');
            }
        });
    });

    // Modal controls
    document.getElementById('cancel-project-modal').addEventListener('click', () => {
        document.getElementById('project-modal').classList.remove('show');
    });

    document.getElementById('close-preview').addEventListener('click', () => {
        document.getElementById('preview-modal').classList.remove('show');
    });

    document.querySelectorAll('.modal-overlay').forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) this.classList.remove('show');
        });
    });
});
</script>
