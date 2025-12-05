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
    <div class="report-card">
        <div class="report-icon"><?= $report['icon'] ?></div>
        <h3 class="report-title"><?= htmlspecialchars($report['title']) ?></h3>
        <p class="report-description"><?= htmlspecialchars($report['description']) ?></p>
        <?php if ($report['lastGenerated']): ?>
        <div class="report-meta">
            Последний: <?= date('d.m.Y', strtotime($report['lastGenerated'])) ?>
        </div>
        <?php endif; ?>
        <button class="btn btn-primary btn-sm" style="width: 100%; margin-top: 16px;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 16px; height: 16px;">
                <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/>
                <polyline points="14,2 14,8 20,8"/>
            </svg>
            Сгенерировать
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
                <th>Дата создания</th>
                <th>Размер</th>
                <th class="no-sort">Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($recentReports as $report): ?>
            <tr>
                <td>
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <span style="font-size: 20px;">📄</span>
                        <span><?= htmlspecialchars($report['name']) ?></span>
                    </div>
                </td>
                <td style="color: var(--text-tertiary);">
                    <?= date('d.m.Y', strtotime($report['date'])) ?>
                </td>
                <td style="font-family: 'JetBrains Mono', monospace; color: var(--text-tertiary);">
                    <?= $report['size'] ?>
                </td>
                <td>
                    <div style="display: flex; gap: 8px;">
                        <button class="btn btn-secondary btn-sm">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 14px; height: 14px;">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                <polyline points="7,10 12,15 17,10"/>
                                <line x1="12" y1="15" x2="12" y2="3"/>
                            </svg>
                            Скачать
                        </button>
                        <button class="btn btn-secondary btn-sm">
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
        </tbody>
    </table>
</div>

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
</style>
