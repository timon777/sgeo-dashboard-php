<div class="page-header animate-on-scroll">
    <div class="page-header-top">
        <div class="page-title-group">
            <h1 class="page-title">Проекты и аналитические направления</h1>
            <p class="page-subtitle">Мониторинг ключевых тематик в ответах языковых моделей</p>
        </div>
        <div class="page-actions">
            <a href="/export/projects" class="btn btn-secondary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                    <polyline points="7,10 12,15 17,10"/>
                    <line x1="12" y1="15" x2="12" y2="3"/>
                </svg>
                Экспорт CSV
            </a>
            <button class="btn btn-primary" id="create-project-btn">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="5" x2="12" y2="19"/>
                    <line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                Создать проект
            </button>
        </div>
    </div>

    <div class="tabs-container" id="project-tabs">
        <button class="tab <?= empty($_GET['type']) ? 'active' : '' ?>" data-filter="all">Все проекты</button>
        <button class="tab <?= ($_GET['type'] ?? '') === 'gov' ? 'active' : '' ?>" data-filter="gov">Государственные</button>
        <button class="tab <?= ($_GET['type'] ?? '') === 'private' ? 'active' : '' ?>" data-filter="private">Частные</button>
    </div>
</div>

<!-- Projects Grid -->
<div class="projects-grid stagger-children" id="projects-grid">
    <?php foreach ($projects as $project): ?>
    <a href="/topics/<?= $project['id'] ?>" class="project-card" data-type="<?= $project['type'] ?>">
        <div class="project-header">
            <div class="project-icon <?= $project['type'] ?>">
                <?= $project['icon'] ?>
            </div>
            <span class="project-badge <?= $project['type'] ?>"><?= $project['badge'] ?></span>
        </div>
        <h3 class="project-title"><?= htmlspecialchars($project['title']) ?></h3>
        <p class="project-description"><?= htmlspecialchars($project['description']) ?></p>
        <div class="project-stats">
            <div class="stat-row">
                <span class="stat-label">Точность представления темы</span>
                <span class="stat-value"><?= $project['accuracy'] ?>%</span>
            </div>
            <div class="progress-bar">
                <div class="progress-fill" style="width: <?= $project['accuracy'] ?>%;"></div>
            </div>
        </div>
        <div class="project-footer">
            <div class="trend-badge <?= $project['trendUp'] ? 'up' : 'down' ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="<?= $project['trendUp'] ? 'm18 15-6-6-6 6' : 'm6 9 6 6 6-6' ?>"/>
                </svg>
                <?= $project['trend'] ?> за неделю
            </div>
            <span class="view-link">
                Подробнее
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M5 12h14"/>
                    <path d="m12 5 7 7-7 7"/>
                </svg>
            </span>
        </div>
    </a>
    <?php endforeach; ?>
</div>

<style>
.projects-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
    gap: 24px;
}

.project-card {
    background: var(--bg-card);
    border: 1px solid var(--border-subtle);
    border-radius: var(--radius-xl);
    padding: 28px;
    text-decoration: none;
    display: flex;
    flex-direction: column;
    transition: all var(--transition-base);
    position: relative;
    overflow: hidden;
}

.project-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: var(--accent-gradient);
    opacity: 0;
    transition: opacity var(--transition-base);
}

.project-card:hover {
    background: var(--bg-card-hover);
    border-color: var(--border-medium);
    transform: translateY(-4px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
}

.project-card:hover::before {
    opacity: 1;
}

.project-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    margin-bottom: 16px;
}

.project-icon {
    width: 48px;
    height: 48px;
    border-radius: var(--radius-md);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
}

.project-icon.gov {
    background: linear-gradient(135deg, rgba(99, 102, 241, 0.2) 0%, rgba(139, 92, 246, 0.2) 100%);
}

.project-icon.private {
    background: linear-gradient(135deg, rgba(34, 197, 94, 0.2) 0%, rgba(16, 185, 129, 0.2) 100%);
}

.project-badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.project-badge.gov {
    background: rgba(99, 102, 241, 0.12);
    color: var(--accent-primary);
}

.project-badge.private {
    background: var(--success-bg);
    color: var(--success);
}

.project-title {
    font-size: 18px;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 8px;
    line-height: 1.4;
}

.project-description {
    font-size: 14px;
    color: var(--text-tertiary);
    line-height: 1.6;
    margin-bottom: 24px;
    flex: 1;
}

.project-stats {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.stat-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.project-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid var(--border-subtle);
}

.view-link {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    font-weight: 500;
    color: var(--accent-primary);
    transition: all var(--transition-fast);
}

.view-link svg {
    width: 16px;
    height: 16px;
    transition: transform var(--transition-fast);
}

.project-card:hover .view-link svg {
    transform: translateX(4px);
}

@media (max-width: 768px) {
    .projects-grid {
        grid-template-columns: 1fr;
    }
}

.project-card.hidden {
    display: none;
}

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

<!-- Toast -->
<div id="toast" class="toast"></div>

<!-- Create Project Modal -->
<div class="modal-overlay" id="create-modal">
    <div class="modal">
        <h3 class="modal-title">Создать проект</h3>
        <form id="create-project-form">
            <div class="form-group">
                <label class="form-label">Название проекта</label>
                <input type="text" class="input-field" id="project-name" required placeholder="Например: Имидж компании">
            </div>
            <div class="form-group">
                <label class="form-label">Описание</label>
                <textarea class="input-field" id="project-description" rows="3" placeholder="Краткое описание проекта"></textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Тип</label>
                <select class="select-field" id="project-type">
                    <option value="gov">Государственный</option>
                    <option value="private">Частный</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Иконка (эмодзи)</label>
                <input type="text" class="input-field" id="project-icon" value="📊" maxlength="2">
            </div>
            <div style="display: flex; gap: 12px; margin-top: 24px;">
                <button type="submit" class="btn btn-primary" style="flex: 1;">Создать</button>
                <button type="button" class="btn btn-secondary" id="cancel-modal">Отмена</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tabs = document.querySelectorAll('#project-tabs .tab');
    const projects = document.querySelectorAll('#projects-grid .project-card');

    // Get initial filter from URL
    const urlParams = new URLSearchParams(window.location.search);
    const initialFilter = urlParams.get('type') || 'all';
    filterProjects(initialFilter);

    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const filter = this.dataset.filter;
            tabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            filterProjects(filter);
            const url = new URL(window.location);
            if (filter === 'all') {
                url.searchParams.delete('type');
            } else {
                url.searchParams.set('type', filter);
            }
            window.history.pushState({}, '', url);
        });
    });

    function filterProjects(filter) {
        projects.forEach(project => {
            if (filter === 'all' || project.dataset.type === filter) {
                project.classList.remove('hidden');
            } else {
                project.classList.add('hidden');
            }
        });
    }

    // Toast
    function showToast(message, type = 'success') {
        const toast = document.getElementById('toast');
        toast.textContent = message;
        toast.className = 'toast ' + type + ' show';
        setTimeout(() => { toast.classList.remove('show'); }, 3000);
    }

    // Modal
    const modal = document.getElementById('create-modal');
    document.getElementById('create-project-btn').addEventListener('click', () => modal.classList.add('show'));
    document.getElementById('cancel-modal').addEventListener('click', () => modal.classList.remove('show'));
    modal.addEventListener('click', (e) => { if (e.target === modal) modal.classList.remove('show'); });

    // Create project
    document.getElementById('create-project-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        const data = {
            name: document.getElementById('project-name').value,
            description: document.getElementById('project-description').value,
            type: document.getElementById('project-type').value,
            icon: document.getElementById('project-icon').value,
            badge: document.getElementById('project-type').value === 'gov' ? 'Гос. партнёр' : 'Частный партнёр'
        };
        try {
            const resp = await fetch('/api/projects', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            const result = await resp.json();
            if (result.success) {
                showToast('Проект создан', 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                showToast(result.error || 'Ошибка создания', 'error');
            }
        } catch (err) {
            showToast('Ошибка сети', 'error');
        }
    });
});
</script>
