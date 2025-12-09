<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SGEO — <?= $pageTitle ?? 'Проект' ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <link rel="stylesheet" href="/assets/css/main.css?v=3">
    <link rel="stylesheet" href="/assets/css/ui-components.css?v=2">
    <script>
        (function() {
            if (localStorage.getItem('sgeo-theme') === 'light') {
                document.documentElement.classList.add('light-theme');
            }
        })();
    </script>
</head>
<body>
    <a href="#main-content" class="skip-link">Перейти к контенту</a>
    <div class="sidebar-overlay" aria-hidden="true"></div>

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
            <!-- Project Header with Breadcrumbs -->
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
                    </div>
                    <button class="global-search-trigger" aria-label="Поиск">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8"/>
                            <path d="m21 21-4.35-4.35"/>
                        </svg>
                        <span>Поиск...</span>
                        <span class="shortcut">⌘K</span>
                    </button>
                    <div class="status-badge">
                        <span class="status-dot"></span>
                        Система активна
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
