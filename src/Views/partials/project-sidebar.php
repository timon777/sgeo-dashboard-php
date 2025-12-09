<aside class="sidebar project-sidebar" role="navigation" aria-label="Меню проекта">
    <div class="sidebar-header">
        <a href="/" class="logo-link" title="На главную">
            <div class="logo-container">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20">
                    <path d="M12 2L2 7l10 5 10-5-10-5z"/>
                    <path d="M2 17l10 5 10-5"/>
                    <path d="M2 12l10 5 10-5"/>
                </svg>
            </div>
            <span class="logo-text">SGEO</span>
        </a>
        <a href="/" class="sidebar-collapse-btn" title="На главную">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18">
                <path d="m15 18-6-6 6-6"/>
            </svg>
        </a>
    </div>

    <!-- Partner Type Label -->
    <div class="partner-type-label">
        <?php
        $typeLabel = 'ПАРТНЁР';
        if (isset($topic['type'])) {
            $typeLabel = $topic['type'] === 'gov' ? 'ГОСУДАРСТВЕННЫЙ ПАРТНЁР' : 'ЧАСТНЫЙ ПАРТНЁР';
        }
        ?>
        <?= $typeLabel ?>
    </div>

    <!-- Project Selector -->
    <div class="project-selector">
        <button class="project-selector-btn" id="projectSelectorBtn">
            <span class="project-name"><?= htmlspecialchars($topic['name'] ?? 'Выберите проект') ?></span>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
                <path d="m7 10 5 5 5-5"/>
            </svg>
        </button>
        <div class="project-dropdown" id="projectDropdown">
            <?php if (!empty($allProjects)): ?>
                <?php foreach ($allProjects as $project): ?>
                <a href="/topics/<?= $project['id'] ?>"
                   class="project-dropdown-item <?= ($topic['id'] ?? '') === $project['id'] ? 'active' : '' ?>">
                    <span class="project-icon"><?= $project['icon'] ?? '📊' ?></span>
                    <span><?= htmlspecialchars($project['name']) ?></span>
                </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Project Navigation -->
    <nav class="sidebar-nav project-nav">
        <a href="/topics/<?= $topic['id'] ?? '' ?>?tab=overview"
           class="nav-item <?= ($activeTab ?? 'overview') === 'overview' ? 'active' : '' ?>">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="3" width="7" height="9" rx="1"/>
                <rect x="14" y="3" width="7" height="5" rx="1"/>
                <rect x="14" y="12" width="7" height="9" rx="1"/>
                <rect x="3" y="16" width="7" height="5" rx="1"/>
            </svg>
            Общий обзор
        </a>
        <a href="/topics/<?= $topic['id'] ?? '' ?>?tab=prompts"
           class="nav-item <?= ($activeTab ?? '') === 'prompts' ? 'active' : '' ?>">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
            </svg>
            Промты
        </a>
        <a href="/topics/<?= $topic['id'] ?? '' ?>?tab=responses"
           class="nav-item <?= ($activeTab ?? '') === 'responses' ? 'active' : '' ?>">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>
            </svg>
            Ответы
        </a>
        <a href="/topics/<?= $topic['id'] ?? '' ?>?tab=sources"
           class="nav-item <?= ($activeTab ?? '') === 'sources' ? 'active' : '' ?>">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
            </svg>
            Источники
        </a>
    </nav>

    <!-- Back to Projects -->
    <div class="sidebar-footer">
        <a href="/projects" class="back-to-projects">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
                <path d="m15 18-6-6 6-6"/>
            </svg>
            Все проекты
        </a>
    </div>
</aside>

<style>
/* Project Sidebar Styles */
.project-sidebar {
    width: 280px;
}

.partner-type-label {
    padding: 8px 20px;
    font-size: 10px;
    font-weight: 600;
    letter-spacing: 0.5px;
    color: var(--text-tertiary);
    text-transform: uppercase;
}

.project-selector {
    padding: 0 16px;
    margin-bottom: 16px;
    position: relative;
}

.project-selector-btn {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 16px;
    background: var(--bg-tertiary);
    border: 1px solid var(--border-subtle);
    border-radius: var(--radius-md);
    color: var(--text-primary);
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all var(--transition-fast);
}

.project-selector-btn:hover {
    background: var(--bg-card);
    border-color: var(--accent-primary);
}

.project-selector-btn .project-name {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    flex: 1;
    text-align: left;
}

.project-dropdown {
    position: absolute;
    top: 100%;
    left: 16px;
    right: 16px;
    background: var(--bg-card);
    border: 1px solid var(--border-subtle);
    border-radius: var(--radius-md);
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
    z-index: 100;
    max-height: 300px;
    overflow-y: auto;
    display: none;
    margin-top: 4px;
}

.project-dropdown.open {
    display: block;
}

.project-dropdown-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 14px;
    color: var(--text-secondary);
    text-decoration: none;
    font-size: 13px;
    transition: all var(--transition-fast);
}

.project-dropdown-item:hover {
    background: var(--bg-tertiary);
    color: var(--text-primary);
}

.project-dropdown-item.active {
    background: rgba(99, 102, 241, 0.1);
    color: var(--accent-primary);
}

.project-dropdown-item .project-icon {
    font-size: 16px;
}

/* Project Nav */
.project-nav {
    padding: 0 8px;
}

.project-nav .nav-item {
    margin-bottom: 2px;
}

/* Sidebar Footer */
.sidebar-footer {
    margin-top: auto;
    padding: 16px;
    border-top: 1px solid var(--border-subtle);
}

.back-to-projects {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 12px;
    color: var(--text-tertiary);
    text-decoration: none;
    font-size: 13px;
    border-radius: var(--radius-md);
    transition: all var(--transition-fast);
}

.back-to-projects:hover {
    background: var(--bg-tertiary);
    color: var(--text-primary);
}

.sidebar-collapse-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 28px;
    height: 28px;
    border-radius: var(--radius-sm);
    color: var(--text-tertiary);
    text-decoration: none;
    transition: all var(--transition-fast);
    margin-left: auto;
}

.sidebar-collapse-btn:hover {
    background: var(--bg-tertiary);
    color: var(--text-primary);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectorBtn = document.getElementById('projectSelectorBtn');
    const dropdown = document.getElementById('projectDropdown');

    if (selectorBtn && dropdown) {
        selectorBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            dropdown.classList.toggle('open');
        });

        document.addEventListener('click', function() {
            dropdown.classList.remove('open');
        });
    }
});
</script>
