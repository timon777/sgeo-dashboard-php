<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SGEO — <?= $pageTitle ?? 'Проект' ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/main.css?v=3">
    <link rel="stylesheet" href="/assets/css/ui-components.css?v=2">
    <link rel="stylesheet" href="/assets/css/responsive.css?v=1">
    <script>
        (function() {
            if (localStorage.getItem('sgeo-theme') === 'light') {
                document.documentElement.classList.add('light-theme');
            }
        })();
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</head>
<body>
    <a href="#main-content" class="skip-link">Перейти к контенту</a>
    <div class="sidebar-overlay" aria-hidden="true"></div>

    <!-- Mobile Menu Toggle -->
    <button class="mobile-menu-toggle" aria-label="Открыть меню" aria-expanded="false">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="3" y1="12" x2="21" y2="12"/>
            <line x1="3" y1="6" x2="21" y2="6"/>
            <line x1="3" y1="18" x2="21" y2="18"/>
        </svg>
    </button>

    <div class="bg-effects">
        <div class="bg-gradient-orb"></div>
        <div class="bg-gradient-orb"></div>
        <div class="bg-gradient-orb"></div>
        <div class="bg-noise"></div>
        <div class="bg-grid"></div>
    </div>

    <div class="app-container">
        <?php include __DIR__ . '/../partials/project-sidebar.php'; ?>

        <main class="main-content">
            <!-- Header with Project Breadcrumbs -->
            <header class="header">
                <div class="header-left">
                    <button class="mobile-menu-btn" aria-label="Открыть меню" aria-expanded="false">
                        <span></span>
                        <span></span>
                        <span></span>
                    </button>
                    <div class="breadcrumbs" aria-label="Навигация">
                        <a href="/" class="breadcrumb-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
                                <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                                <polyline points="9 22 9 12 15 12 15 22"/>
                            </svg>
                        </a>
                        <span class="breadcrumb-separator" aria-hidden="true">/</span>
                        <a href="/projects" class="breadcrumb-item">Проекты и аналитические направления</a>
                        <span class="breadcrumb-separator" aria-hidden="true">/</span>
                        <span class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($topic['name'] ?? 'Проект') ?></span>
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
                    <div class="user-menu">
                        <button class="user-menu-trigger" aria-label="Меню пользователя">
                            <div class="user-avatar">
                                <?= htmlspecialchars($_SESSION['user_avatar'] ?? 'U') ?>
                            </div>
                            <span class="user-name"><?= htmlspecialchars($_SESSION['user_name'] ?? 'Пользователь') ?></span>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
                                <path d="m6 9 6 6 6-6"/>
                            </svg>
                        </button>
                        <div class="user-dropdown">
                            <div class="user-dropdown-header">
                                <div class="user-avatar"><?= htmlspecialchars($_SESSION['user_avatar'] ?? 'U') ?></div>
                                <div class="user-info">
                                    <div class="user-name"><?= htmlspecialchars($_SESSION['user_name'] ?? 'Пользователь') ?></div>
                                    <div class="user-email"><?= htmlspecialchars($_SESSION['user_email'] ?? '') ?></div>
                                </div>
                            </div>
                            <div class="user-dropdown-divider"></div>
                            <a href="/settings" class="user-dropdown-item">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18">
                                    <circle cx="12" cy="12" r="3"/><path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/>
                                </svg>
                                Настройки
                            </a>
                            <a href="/logout" class="user-dropdown-item user-dropdown-logout">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18">
                                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/>
                                </svg>
                                Выйти
                            </a>
                        </div>
                    </div>
                </div>
            </header>

            <div class="content" id="main-content">
                <?= $content ?? '' ?>
            </div>
        </main>
    </div>

    <script src="/assets/js/ui-components.js?v=2"></script>
    <script src="/assets/js/main.js?v=2"></script>
    <?php if (isset($pageScripts)): ?>
        <?= $pageScripts ?>
    <?php endif; ?>
</body>
</html>
