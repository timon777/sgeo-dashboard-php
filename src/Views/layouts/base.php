<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SGEO — <?= $pageTitle ?? 'Дашборд' ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/main.css?v=3">
    <link rel="stylesheet" href="/assets/css/ui-components.css?v=2">
    <link rel="stylesheet" href="/assets/css/responsive.css?v=6">
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
        <?php include __DIR__ . '/../partials/sidebar.php'; ?>

        <main class="main-content">
            <?php include __DIR__ . '/../partials/header.php'; ?>

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
