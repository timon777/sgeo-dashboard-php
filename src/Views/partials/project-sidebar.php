<aside class="sidebar project-sidebar" role="navigation" aria-label="Меню проекта">
    <div class="sidebar-header">
        <div class="logo-container">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20">
                <path d="M12 2L2 7l10 5 10-5-10-5z"/>
                <path d="M2 17l10 5 10-5"/>
                <path d="M2 12l10 5 10-5"/>
            </svg>
        </div>
        <span class="logo-text">SGEO</span>
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
        <a href="/topics/<?= $topic['id'] ?? '' ?>"
           class="nav-item <?= ($activeTab ?? '') === 'overview' || !isset($activeTab) || $activeTab === 'responses' ? 'active' : '' ?>">
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
                <circle cx="12" cy="12" r="10"/>
                <path d="M12 16v-4"/>
                <path d="M12 8h.01"/>
            </svg>
            Промты
        </a>
        <a href="/topics/<?= $topic['id'] ?? '' ?>?tab=responses"
           class="nav-item <?= ($activeTab ?? '') === 'responses' ? 'active' : '' ?>">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"/>
                <path d="M12 16v-4"/>
                <path d="M12 8h.01"/>
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
