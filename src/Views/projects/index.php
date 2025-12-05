<div class="page-header animate-on-scroll">
    <div class="page-header-top">
        <div class="page-title-group">
            <h1 class="page-title">Проекты и аналитические направления</h1>
            <p class="page-subtitle">Мониторинг ключевых тематик в ответах языковых моделей</p>
        </div>
        <button class="btn btn-primary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="12" y1="5" x2="12" y2="19"/>
                <line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Создать проект
        </button>
    </div>

    <div class="tabs-container">
        <button class="tab active">Все проекты</button>
        <button class="tab">Государственные</button>
        <button class="tab">Частные</button>
    </div>
</div>

<!-- Projects Grid -->
<div class="projects-grid stagger-children">
    <?php foreach ($projects as $project): ?>
    <a href="/projects/<?= $project['id'] ?>" class="project-card">
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
</style>
