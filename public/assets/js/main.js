/* =====================================================
   SGEO Dashboard - Main JavaScript
   ===================================================== */

// Theme and Language toggles
let currentLang = localStorage.getItem('sgeo-lang') || 'ru';

function toggleTheme() {
    const isLight = !document.documentElement.classList.contains('light-theme');
    localStorage.setItem('sgeo-theme', isLight ? 'light' : 'dark');

    // Reload page to reinitialize charts with correct theme colors
    location.reload();
}

function toggleLanguage() {
    currentLang = currentLang === 'ru' ? 'en' : 'ru';
    localStorage.setItem('sgeo-lang', currentLang);
    document.querySelectorAll('.lang-toggle span').forEach(s => s.textContent = currentLang === 'ru' ? 'Рус' : 'Eng');
    if (typeof SGEO !== 'undefined' && SGEO.Toast) {
        SGEO.Toast.info(currentLang === 'ru' ? 'Язык: Русский' : 'Language: English', '');
    }
}

// Accordion toggle
function toggleAccordion(header) {
    const item = header.closest('.accordion-item');
    const wasActive = item.classList.contains('active');

    // Close all items
    document.querySelectorAll('.accordion-item').forEach(i => i.classList.remove('active'));

    // Open clicked item if it wasn't active
    if (!wasActive) {
        item.classList.add('active');
    }
}

// Submenu toggle
function toggleSubmenu(menuId, event) {
    event.preventDefault();
    const submenu = document.getElementById(menuId);
    const navItem = event.currentTarget;

    if (submenu) {
        submenu.classList.toggle('open');
        navItem.classList.toggle('expanded');
    }
}

// Chart theme helpers
function isLightTheme() {
    return document.documentElement.classList.contains('light-theme');
}

function getChartColors() {
    const light = isLightTheme();
    return {
        text: light ? 'rgba(26, 28, 34, 0.6)' : 'rgba(255, 255, 255, 0.6)',
        textStrong: light ? 'rgba(26, 28, 34, 0.8)' : 'rgba(255, 255, 255, 0.8)',
        grid: light ? 'rgba(0, 0, 0, 0.06)' : 'rgba(255, 255, 255, 0.06)',
        gridStrong: light ? 'rgba(0, 0, 0, 0.1)' : 'rgba(255, 255, 255, 0.1)',
    };
}

function updateChartDefaults() {
    const colors = getChartColors();

    if (typeof Chart !== 'undefined') {
        Chart.defaults.color = colors.text;
        Chart.defaults.borderColor = colors.grid;

        // Font settings
        Chart.defaults.font.family = "'Outfit', sans-serif";
        Chart.defaults.font.size = 12;
    }
}

// Page charts storage for cleanup
window.pageCharts = {};

function destroyPageCharts() {
    Object.values(window.pageCharts).forEach(chart => {
        if (chart && typeof chart.destroy === 'function') {
            chart.destroy();
        }
    });
    window.pageCharts = {};
}

// Initialize on DOM load
document.addEventListener('DOMContentLoaded', function() {
    // Initialize language display
    const savedLang = localStorage.getItem('sgeo-lang');
    if (savedLang) {
        currentLang = savedLang;
        document.querySelectorAll('.lang-toggle span').forEach(s => s.textContent = currentLang === 'ru' ? 'Рус' : 'Eng');
    }

    // Tab switching
    document.querySelectorAll('.tabs-container').forEach(container => {
        const tabs = container.querySelectorAll('.tab');
        tabs.forEach(tab => {
            tab.addEventListener('click', function() {
                tabs.forEach(t => t.classList.remove('active'));
                this.classList.add('active');
            });
        });
    });

    // Filter buttons
    document.querySelectorAll('.filters-row').forEach(container => {
        const filters = container.querySelectorAll('.filter-btn');
        filters.forEach(filter => {
            filter.addEventListener('click', function() {
                if (this.dataset.group) {
                    container.querySelectorAll(`.filter-btn[data-group="${this.dataset.group}"]`).forEach(f => f.classList.remove('active'));
                }
                this.classList.toggle('active');
            });
        });
    });

    // Update chart defaults
    updateChartDefaults();

    // User menu toggle
    const userMenuTrigger = document.querySelector('.user-menu-trigger');
    const userMenu = document.querySelector('.user-menu');

    if (userMenuTrigger && userMenu) {
        userMenuTrigger.addEventListener('click', function(e) {
            e.stopPropagation();
            userMenu.classList.toggle('open');
        });

        // Close on click outside
        document.addEventListener('click', function(e) {
            if (!userMenu.contains(e.target)) {
                userMenu.classList.remove('open');
            }
        });

        // Close on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                userMenu.classList.remove('open');
            }
        });
    }
});

// Export functions globally
window.toggleTheme = toggleTheme;
window.toggleLanguage = toggleLanguage;
window.toggleAccordion = toggleAccordion;
window.toggleSubmenu = toggleSubmenu;
window.isLightTheme = isLightTheme;
window.getChartColors = getChartColors;
window.updateChartDefaults = updateChartDefaults;
