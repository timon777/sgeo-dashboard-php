<aside class="sidebar" role="navigation" aria-label="Главное меню">
    <div class="sidebar-header">
        <div class="logo-container">
            <span class="logo-icon">S</span>
        </div>
        <span class="logo-text">SGEO</span>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section">
            <div class="nav-section-title">Навигация</div>
            <a href="/" class="nav-item <?= ($currentPage ?? '') === 'dashboard' ? 'active' : '' ?>">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="7" height="9" rx="1"/>
                    <rect x="14" y="3" width="7" height="5" rx="1"/>
                    <rect x="14" y="12" width="7" height="9" rx="1"/>
                    <rect x="3" y="16" width="7" height="5" rx="1"/>
                </svg>
                Дашборд
            </a>
            <a href="/projects" class="nav-item <?= ($currentPage ?? '') === 'projects' ? 'active' : '' ?>">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/>
                </svg>
                Проекты
                <span class="nav-badge"><?= $projectCount ?? 7 ?></span>
            </a>
            <a href="/prompts" class="nav-item <?= ($currentPage ?? '') === 'prompts' ? 'active' : '' ?>">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                </svg>
                Промты
            </a>
            <a href="/sources" class="nav-item <?= ($currentPage ?? '') === 'sources' ? 'active' : '' ?>">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                </svg>
                Источники
            </a>
        </div>

        <div class="nav-divider"></div>

        <div class="nav-section">
            <div class="nav-section-title">Аналитика</div>
            <a href="/llm-monitoring" class="nav-item <?= ($currentPage ?? '') === 'llm-monitoring' ? 'active' : '' ?>">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="3"/>
                    <path d="M12 1v6m0 6v10"/>
                    <path d="m4.93 4.93 4.24 4.24m5.66 5.66 4.24 4.24"/>
                    <path d="M1 12h6m6 0h10"/>
                    <path d="m4.93 19.07 4.24-4.24m5.66-5.66 4.24-4.24"/>
                </svg>
                LLM Мониторинг
            </a>
            <a href="/trends" class="nav-item <?= ($currentPage ?? '') === 'trends' ? 'active' : '' ?>">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 3v18h18"/>
                    <path d="m19 9-5 5-4-4-3 3"/>
                </svg>
                Тренды
            </a>
            <a href="/reports" class="nav-item <?= ($currentPage ?? '') === 'reports' ? 'active' : '' ?>">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/>
                    <polyline points="14,2 14,8 20,8"/>
                    <line x1="16" y1="13" x2="8" y2="13"/>
                    <line x1="16" y1="17" x2="8" y2="17"/>
                    <line x1="10" y1="9" x2="8" y2="9"/>
                </svg>
                Отчёты
            </a>
        </div>

        <div class="nav-divider"></div>

        <div class="nav-section">
            <div class="nav-section-title">Система</div>
            <a href="/settings" class="nav-item <?= ($currentPage ?? '') === 'settings' ? 'active' : '' ?>">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="3"/>
                    <path d="M12 1v2m0 18v2M4.22 4.22l1.42 1.42m12.72 12.72l1.42 1.42M1 12h2m18 0h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/>
                </svg>
                Настройки
            </a>
        </div>

        <div class="nav-divider"></div>

        <div class="nav-section">
            <div class="nav-section-title">Партнёры</div>
            <a href="#" class="nav-item" onclick="toggleSubmenu('private-menu', event)">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
                Частный партнёр
                <svg class="expand-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="m6 9 6 6 6-6"/>
                </svg>
            </a>
            <div id="private-menu" class="nav-submenu">
                <a href="/projects/freedom-bank" class="nav-item">Freedom Bank</a>
                <a href="/projects/freedom-broker" class="nav-item">Freedom Broker</a>
            </div>
            <a href="#" class="nav-item" onclick="toggleSubmenu('gov-menu', event)">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 21h18"/>
                    <path d="M5 21V7l8-4v18"/>
                    <path d="M19 21V11l-6-4"/>
                    <path d="M9 9v.01"/>
                    <path d="M9 12v.01"/>
                    <path d="M9 15v.01"/>
                    <path d="M9 18v.01"/>
                </svg>
                Государственный партнёр
                <svg class="expand-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="m6 9 6 6 6-6"/>
                </svg>
            </a>
            <div id="gov-menu" class="nav-submenu">
                <a href="/projects/ministry-info" class="nav-item">Министерство информации</a>
                <a href="/projects/president-image" class="nav-item">Имидж Президента</a>
            </div>
        </div>
    </nav>

    <div class="user-section">
        <div class="user-card">
            <div class="user-avatar">АК</div>
            <div class="user-info">
                <div class="user-name">Администратор</div>
                <div class="user-role">Super Admin</div>
            </div>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: var(--text-tertiary)">
                <path d="m6 9 6 6 6-6"/>
            </svg>
        </div>
    </div>
</aside>
