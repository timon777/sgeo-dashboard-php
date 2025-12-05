<header class="header">
    <div class="header-left">
        <button class="mobile-menu-btn" aria-label="Открыть меню" aria-expanded="false">
            <span></span>
            <span></span>
            <span></span>
        </button>
        <div class="breadcrumbs" aria-label="Навигация">
            <a href="/" class="breadcrumb-item">SGEO</a>
            <span class="breadcrumb-separator" aria-hidden="true">/</span>
            <span class="breadcrumb-item active" aria-current="page"><?= $breadcrumb ?? 'Дашборд' ?></span>
        </div>
    </div>
    <div class="header-right">
        <div class="settings-toggles">
            <button class="theme-toggle" onclick="toggleTheme()" title="Переключить тему">
                <svg class="icon-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
                </svg>
                <svg class="icon-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/>
                    <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/>
                    <line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/>
                    <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>
                </svg>
            </button>
            <button class="lang-toggle" onclick="toggleLanguage()" title="Switch language">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/>
                    <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
                </svg>
                <span>Рус</span>
            </button>
        </div>
        <button class="global-search-trigger" aria-label="Поиск">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8"/>
                <path d="m21 21-4.35-4.35"/>
            </svg>
            <span>Поиск...</span>
            <span class="shortcut">⌘K</span>
        </button>
        <button class="header-btn" data-tooltip="Уведомления" data-tooltip-pos="bottom" aria-label="Уведомления">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
            </svg>
            <span class="notification-dot"></span>
        </button>
        <div class="status-badge">
            <span class="status-dot"></span>
            Система активна
        </div>
    </div>
</header>
