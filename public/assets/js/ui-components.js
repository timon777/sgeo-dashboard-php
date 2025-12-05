/* =====================================================
   SGEO UI Components JavaScript
   Version: 2.0
   ===================================================== */

// =====================================================
// 1. MOBILE MENU
// =====================================================

function initMobileMenu() {
    const menuBtn = document.querySelector('.mobile-menu-btn');
    const sidebar = document.querySelector('.sidebar');
    const overlay = document.querySelector('.sidebar-overlay');

    if (!menuBtn || !sidebar) return;

    menuBtn.addEventListener('click', () => {
        menuBtn.classList.toggle('active');
        sidebar.classList.toggle('open');
        overlay?.classList.toggle('active');
        document.body.style.overflow = sidebar.classList.contains('open') ? 'hidden' : '';
    });

    overlay?.addEventListener('click', () => {
        menuBtn.classList.remove('active');
        sidebar.classList.remove('open');
        overlay.classList.remove('active');
        document.body.style.overflow = '';
    });

    // Close on escape
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && sidebar.classList.contains('open')) {
            menuBtn.classList.remove('active');
            sidebar.classList.remove('open');
            overlay?.classList.remove('active');
            document.body.style.overflow = '';
        }
    });
}

// =====================================================
// 2. GLOBAL SEARCH
// =====================================================

const searchData = [
    { title: 'Дашборд', desc: 'Главная страница аналитики', icon: 'dashboard', url: 'index.html', category: 'Страницы' },
    { title: 'Проекты', desc: 'Список всех проектов', icon: 'folder', url: 'projects.html', category: 'Страницы' },
    { title: 'Промты', desc: 'Таблица промтов и ответов', icon: 'message', url: 'prompts.html', category: 'Страницы' },
    { title: 'Источники', desc: 'База источников с E-E-A-T', icon: 'book', url: 'sources.html', category: 'Страницы' },
    { title: 'LLM Мониторинг', desc: 'Анализ языковых моделей', icon: 'monitor', url: 'llm-monitoring.html', category: 'Страницы' },
    { title: 'Тренды', desc: 'Динамика и аналитика', icon: 'trending', url: 'trends.html', category: 'Страницы' },
    { title: 'Отчёты', desc: 'Генерация отчётов', icon: 'file', url: 'reports.html', category: 'Страницы' },
    { title: 'Настройки', desc: 'Конфигурация системы', icon: 'settings', url: 'settings.html', category: 'Страницы' },
    { title: 'Имидж Президента', desc: 'Проект мониторинга', icon: 'project', url: 'project-overview.html', category: 'Проекты' },
    { title: 'Январские события', desc: 'Проект мониторинга', icon: 'project', url: 'project-overview.html', category: 'Проекты' },
    { title: 'Цифровой Казахстан', desc: 'Проект мониторинга', icon: 'project', url: 'project-overview.html', category: 'Проекты' },
];

const icons = {
    dashboard: '<rect x="3" y="3" width="7" height="9" rx="1"/><rect x="14" y="3" width="7" height="5" rx="1"/><rect x="14" y="12" width="7" height="9" rx="1"/><rect x="3" y="16" width="7" height="5" rx="1"/>',
    folder: '<path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/>',
    message: '<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>',
    book: '<path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>',
    monitor: '<circle cx="12" cy="12" r="3"/><path d="M12 1v6m0 6v10"/>',
    trending: '<path d="M3 3v18h18"/><path d="m19 9-5 5-4-4-3 3"/>',
    file: '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14,2 14,8 20,8"/>',
    settings: '<circle cx="12" cy="12" r="3"/><path d="M12 1v2m0 18v2M4.22 4.22l1.42 1.42m12.72 12.72l1.42 1.42M1 12h2m18 0h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/>',
    project: '<path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/>',
    search: '<circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>'
};

function initGlobalSearch() {
    // Create search modal
    const modal = document.createElement('div');
    modal.className = 'search-modal';
    modal.innerHTML = `
        <div class="search-container">
            <div class="search-box">
                <div class="search-input-wrapper">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    <input type="text" class="search-input" placeholder="Поиск по SGEO..." autofocus>
                    <button class="search-close">ESC</button>
                </div>
                <div class="search-results"></div>
            </div>
        </div>
    `;
    document.body.appendChild(modal);

    const input = modal.querySelector('.search-input');
    const results = modal.querySelector('.search-results');
    const closeBtn = modal.querySelector('.search-close');
    let selectedIndex = -1;

    function openSearch() {
        modal.classList.add('active');
        input.focus();
        input.value = '';
        renderResults('');
    }

    function closeSearch() {
        modal.classList.remove('active');
        selectedIndex = -1;
    }

    function renderResults(query) {
        const filtered = query 
            ? searchData.filter(item => 
                item.title.toLowerCase().includes(query.toLowerCase()) ||
                item.desc.toLowerCase().includes(query.toLowerCase())
              )
            : searchData;

        const grouped = filtered.reduce((acc, item) => {
            if (!acc[item.category]) acc[item.category] = [];
            acc[item.category].push(item);
            return acc;
        }, {});

        if (Object.keys(grouped).length === 0) {
            results.innerHTML = `
                <div class="search-empty">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    <p>Ничего не найдено</p>
                </div>
            `;
            return;
        }

        let html = '';
        let idx = 0;
        for (const [category, items] of Object.entries(grouped)) {
            html += `<div class="search-section"><div class="search-section-title">${category}</div>`;
            for (const item of items) {
                html += `
                    <a href="${item.url}" class="search-item" data-index="${idx}">
                        <div class="search-item-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">${icons[item.icon]}</svg>
                        </div>
                        <div class="search-item-content">
                            <div class="search-item-title">${item.title}</div>
                            <div class="search-item-desc">${item.desc}</div>
                        </div>
                    </a>
                `;
                idx++;
            }
            html += '</div>';
        }
        results.innerHTML = html;
        selectedIndex = -1;
    }

    function updateSelection() {
        const items = results.querySelectorAll('.search-item');
        items.forEach((item, i) => {
            item.classList.toggle('selected', i === selectedIndex);
        });
        if (selectedIndex >= 0 && items[selectedIndex]) {
            items[selectedIndex].scrollIntoView({ block: 'nearest' });
        }
    }

    // Event listeners
    document.querySelectorAll('.global-search-trigger').forEach(trigger => {
        trigger.addEventListener('click', openSearch);
    });

    closeBtn.addEventListener('click', closeSearch);
    modal.addEventListener('click', (e) => {
        if (e.target === modal) closeSearch();
    });

    input.addEventListener('input', (e) => {
        renderResults(e.target.value);
    });

    document.addEventListener('keydown', (e) => {
        // Cmd/Ctrl + K to open
        if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
            e.preventDefault();
            if (modal.classList.contains('active')) {
                closeSearch();
            } else {
                openSearch();
            }
        }

        if (!modal.classList.contains('active')) return;

        const items = results.querySelectorAll('.search-item');

        if (e.key === 'Escape') {
            closeSearch();
        } else if (e.key === 'ArrowDown') {
            e.preventDefault();
            selectedIndex = Math.min(selectedIndex + 1, items.length - 1);
            updateSelection();
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            selectedIndex = Math.max(selectedIndex - 1, 0);
            updateSelection();
        } else if (e.key === 'Enter' && selectedIndex >= 0) {
            e.preventDefault();
            items[selectedIndex]?.click();
        }
    });
}

// =====================================================
// 3. TOAST NOTIFICATIONS
// =====================================================

class Toast {
    static container = null;

    static init() {
        if (!this.container) {
            this.container = document.createElement('div');
            this.container.className = 'toast-container';
            this.container.setAttribute('aria-live', 'polite');
            document.body.appendChild(this.container);
        }
    }

    static show(options) {
        this.init();

        const { type = 'info', title, message, duration = 5000 } = options;

        const iconsSvg = {
            success: '<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>',
            error: '<circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>',
            warning: '<path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>',
            info: '<circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/>'
        };

        const toast = document.createElement('div');
        toast.className = `toast ${type}`;
        toast.innerHTML = `
            <svg class="toast-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                ${iconsSvg[type]}
            </svg>
            <div class="toast-content">
                ${title ? `<div class="toast-title">${title}</div>` : ''}
                ${message ? `<div class="toast-message">${message}</div>` : ''}
            </div>
            <button class="toast-close" aria-label="Закрыть">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        `;

        this.container.appendChild(toast);

        const close = () => {
            toast.classList.add('removing');
            setTimeout(() => toast.remove(), 300);
        };

        toast.querySelector('.toast-close').addEventListener('click', close);

        if (duration > 0) {
            setTimeout(close, duration);
        }

        return { close };
    }

    static success(title, message) { return this.show({ type: 'success', title, message }); }
    static error(title, message) { return this.show({ type: 'error', title, message }); }
    static warning(title, message) { return this.show({ type: 'warning', title, message }); }
    static info(title, message) { return this.show({ type: 'info', title, message }); }
}

// Export SGEO immediately after Toast class definition
window.SGEO = window.SGEO || {};
window.SGEO.Toast = Toast;

// =====================================================
// 4. SORTABLE TABLES
// =====================================================

function initSortableTables() {
    document.querySelectorAll('table').forEach(table => {
        const headers = table.querySelectorAll('th');
        const tbody = table.querySelector('tbody');
        if (!tbody) return;

        headers.forEach((header, index) => {
            // Skip columns that shouldn't be sortable
            if (header.classList.contains('no-sort')) return;

            header.classList.add('sortable');
            header.setAttribute('tabindex', '0');
            header.setAttribute('role', 'columnheader');
            header.setAttribute('aria-sort', 'none');

            const handleSort = () => {
                const isAsc = header.classList.contains('asc');
                
                // Reset all headers
                headers.forEach(h => {
                    h.classList.remove('asc', 'desc');
                    h.setAttribute('aria-sort', 'none');
                });

                // Set new sort direction
                header.classList.add(isAsc ? 'desc' : 'asc');
                header.setAttribute('aria-sort', isAsc ? 'descending' : 'ascending');

                // Sort rows
                const rows = Array.from(tbody.querySelectorAll('tr'));
                const direction = isAsc ? -1 : 1;

                rows.sort((a, b) => {
                    const aVal = a.cells[index]?.textContent.trim() || '';
                    const bVal = b.cells[index]?.textContent.trim() || '';

                    // Try numeric sort
                    const aNum = parseFloat(aVal.replace(/[^\d.-]/g, ''));
                    const bNum = parseFloat(bVal.replace(/[^\d.-]/g, ''));

                    if (!isNaN(aNum) && !isNaN(bNum)) {
                        return (aNum - bNum) * direction;
                    }

                    return aVal.localeCompare(bVal, 'ru') * direction;
                });

                rows.forEach(row => tbody.appendChild(row));

                // Show toast
                Toast.info('Сортировка', `По столбцу "${header.textContent.trim()}"`);
            };

            header.addEventListener('click', handleSort);
            header.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    handleSort();
                }
            });
        });
    });
}

// =====================================================
// 5. SCROLL ANIMATIONS
// =====================================================

function initScrollAnimations() {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    });

    document.querySelectorAll('.animate-on-scroll, .stagger-children').forEach(el => {
        observer.observe(el);
    });
}

// =====================================================
// 6. BUTTON RIPPLE EFFECT
// =====================================================

function initButtonRipple() {
    document.querySelectorAll('.btn, .btn-primary, .btn-secondary').forEach(btn => {
        btn.addEventListener('click', function(e) {
            const rect = this.getBoundingClientRect();
            const ripple = document.createElement('span');
            ripple.className = 'btn-ripple';
            ripple.style.left = (e.clientX - rect.left) + 'px';
            ripple.style.top = (e.clientY - rect.top) + 'px';
            this.appendChild(ripple);
            setTimeout(() => ripple.remove(), 600);
        });
    });
}

// =====================================================
// 7. SKELETON LOADERS
// =====================================================

function showSkeleton(container, type = 'card') {
    const skeletons = {
        card: `
            <div class="skeleton-card">
                <div class="skeleton skeleton-text lg"></div>
                <div class="skeleton skeleton-text md"></div>
                <div class="skeleton skeleton-text sm"></div>
                <div class="skeleton skeleton-chart"></div>
            </div>
        `,
        table: `
            <div class="skeleton-table-row">
                <div class="skeleton skeleton-table-cell" style="width: 30%"></div>
                <div class="skeleton skeleton-table-cell" style="width: 20%"></div>
                <div class="skeleton skeleton-table-cell" style="width: 15%"></div>
                <div class="skeleton skeleton-table-cell" style="width: 35%"></div>
            </div>
        `.repeat(5),
        stats: `
            <div class="skeleton-card" style="display: flex; gap: 12px; align-items: center;">
                <div class="skeleton skeleton-avatar"></div>
                <div style="flex: 1;">
                    <div class="skeleton skeleton-text md"></div>
                    <div class="skeleton skeleton-text sm"></div>
                </div>
            </div>
        `
    };

    container.innerHTML = skeletons[type] || skeletons.card;
}

function hideSkeleton(container, content) {
    container.innerHTML = content;
    container.style.animation = 'fadeIn 0.3s ease';
}

// =====================================================
// 8. INITIALIZE ALL
// =====================================================

document.addEventListener('DOMContentLoaded', () => {
    initMobileMenu();
    initGlobalSearch();
    initSortableTables();
    initScrollAnimations();
    initButtonRipple();

    // Demo toast on load (remove in production)
    // Toast.success('Добро пожаловать', 'Система SGEO загружена успешно');
});

// Export for use in other scripts
window.SGEO = window.SGEO || {};
window.SGEO.Toast = Toast;
window.SGEO.showSkeleton = showSkeleton;
window.SGEO.hideSkeleton = hideSkeleton;
