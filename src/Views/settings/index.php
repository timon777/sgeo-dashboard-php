<div class="page-header animate-on-scroll">
    <div class="page-header-top">
        <div class="page-title-group">
            <h1 class="page-title">Настройки</h1>
            <p class="page-subtitle">Конфигурация системы и управление интеграциями</p>
        </div>
    </div>

    <div class="tabs-container" id="settings-tabs">
        <button class="tab active" data-tab="general">Основные</button>
        <button class="tab" data-tab="monitoring">Мониторинг</button>
        <button class="tab" data-tab="api">API ключи</button>
        <button class="tab" data-tab="security">Безопасность</button>
    </div>
</div>

<!-- Toast notification -->
<div id="toast" class="toast"></div>

<!-- General Settings -->
<div class="settings-section tab-content active" id="tab-general">
    <div class="settings-card">
        <h3 class="settings-title">Общие настройки</h3>
        <p class="settings-description">Основные параметры работы системы</p>

        <form id="general-form">
            <div class="form-group">
                <label class="form-label">Название организации</label>
                <input type="text" class="input-field" id="organization_name" value="<?= htmlspecialchars($settings['organization_name']) ?>">
            </div>

            <div class="form-group">
                <label class="form-label">Email администратора</label>
                <input type="email" class="input-field" id="admin_email" value="<?= htmlspecialchars($settings['admin_email']) ?>">
            </div>

            <div class="form-group">
                <label class="form-label">Часовой пояс</label>
                <select class="select-field" id="timezone">
                    <option value="Asia/Almaty" <?= $settings['timezone'] === 'Asia/Almaty' ? 'selected' : '' ?>>Asia/Almaty (UTC+5)</option>
                    <option value="Asia/Astana" <?= $settings['timezone'] === 'Asia/Astana' ? 'selected' : '' ?>>Asia/Astana (UTC+6)</option>
                    <option value="Europe/Moscow" <?= $settings['timezone'] === 'Europe/Moscow' ? 'selected' : '' ?>>Europe/Moscow (UTC+3)</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Язык интерфейса</label>
                <select class="select-field" id="language">
                    <option value="ru" <?= $settings['language'] === 'ru' ? 'selected' : '' ?>>Русский</option>
                    <option value="en" <?= $settings['language'] === 'en' ? 'selected' : '' ?>>English</option>
                    <option value="kz" <?= $settings['language'] === 'kz' ? 'selected' : '' ?>>Қазақша</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary" style="margin-top: 8px;">Сохранить изменения</button>
        </form>
    </div>
</div>

<!-- Monitoring Settings -->
<div class="settings-section tab-content" id="tab-monitoring">
    <div class="settings-card">
        <h3 class="settings-title">Настройки мониторинга</h3>
        <p class="settings-description">Параметры отслеживания и анализа</p>

        <form id="monitoring-form">
            <div class="form-group">
                <label class="form-label">Частота проверки</label>
                <select class="select-field" id="check_frequency">
                    <option value="daily" <?= $settings['check_frequency'] === 'daily' ? 'selected' : '' ?>>Раз в день</option>
                    <option value="weekly" <?= $settings['check_frequency'] === 'weekly' ? 'selected' : '' ?>>Раз в неделю</option>
                    <option value="monthly" <?= $settings['check_frequency'] === 'monthly' ? 'selected' : '' ?>>Раз в месяц</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Минимальный порог оценки</label>
                <input type="number" class="input-field" id="min_score_threshold" value="<?= (int)$settings['min_score_threshold'] ?>">
            </div>

            <div class="form-group">
                <label class="form-label">Уведомлять при изменении рейтинга на (%)</label>
                <input type="number" class="input-field" id="rating_change_notify" value="<?= (int)$settings['rating_change_notify'] ?>">
            </div>

            <button type="submit" class="btn btn-primary" style="margin-top: 8px;">Сохранить изменения</button>
        </form>
    </div>
</div>

<!-- API Settings -->
<div class="settings-section tab-content" id="tab-api">
    <div class="settings-card">
        <h3 class="settings-title">API ключи LLM</h3>
        <p class="settings-description">Управление ключами доступа к языковым моделям</p>

        <div class="api-keys-list" id="api-keys-list">
            <?php foreach ($apiKeys as $key): ?>
            <div class="api-key-item" data-id="<?= $key['id'] ?>">
                <div class="api-key-info">
                    <div class="api-key-name"><?= htmlspecialchars($key['name']) ?></div>
                    <div class="api-key-value"><?= htmlspecialchars($key['key']) ?>****</div>
                </div>
                <div class="api-key-status <?= $key['status'] ?>">
                    <?= $key['status'] === 'active' ? 'Активен' : 'Неактивен' ?>
                </div>
                <div class="api-key-actions">
                    <button class="btn btn-secondary btn-sm delete-api-key" style="color: var(--danger);">Удалить</button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <button class="btn btn-primary" style="margin-top: 16px;" id="add-api-key-btn">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 16px; height: 16px;">
                <line x1="12" y1="5" x2="12" y2="19"/>
                <line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Добавить API ключ
        </button>

        <!-- Add API Key Form (hidden by default) -->
        <div id="add-api-key-form" style="display: none; margin-top: 20px; padding-top: 20px; border-top: 1px solid var(--border-subtle);">
            <h4 style="margin-bottom: 16px; color: var(--text-primary);">Новый API ключ</h4>
            <form id="api-key-form">
                <div class="form-group">
                    <label class="form-label">Провайдер</label>
                    <select class="select-field" id="api_provider">
                        <option value="openai">OpenAI</option>
                        <option value="anthropic">Anthropic</option>
                        <option value="google">Google</option>
                        <option value="perplexity">Perplexity</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Название</label>
                    <input type="text" class="input-field" id="api_name" placeholder="Например: Production Key">
                </div>
                <div class="form-group">
                    <label class="form-label">API ключ</label>
                    <input type="password" class="input-field" id="api_key_value" placeholder="sk-...">
                </div>
                <div style="display: flex; gap: 8px;">
                    <button type="submit" class="btn btn-primary">Добавить</button>
                    <button type="button" class="btn btn-secondary" id="cancel-api-key">Отмена</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Security Settings -->
<div class="settings-section tab-content" id="tab-security">
    <div class="settings-card">
        <h3 class="settings-title">Безопасность</h3>
        <p class="settings-description">Настройки безопасности аккаунта</p>

        <form id="security-form">
            <div class="form-group">
                <label class="form-label">Текущий пароль</label>
                <input type="password" class="input-field" id="current_password" placeholder="Введите текущий пароль">
            </div>

            <div class="form-group">
                <label class="form-label">Новый пароль</label>
                <input type="password" class="input-field" id="new_password" placeholder="Введите новый пароль">
            </div>

            <div class="form-group">
                <label class="form-label">Подтвердите пароль</label>
                <input type="password" class="input-field" id="confirm_password" placeholder="Повторите новый пароль">
            </div>

            <button type="submit" class="btn btn-primary" style="margin-top: 8px;">Изменить пароль</button>
        </form>
    </div>
</div>

<!-- Danger Zone -->
<div class="settings-section danger-zone">
    <div class="settings-card" style="border-color: var(--danger); border-width: 2px;">
        <h3 class="settings-title" style="color: var(--danger);">Опасная зона</h3>
        <p class="settings-description">Необратимые действия с системой</p>

        <div class="danger-actions">
            <div class="danger-action">
                <div>
                    <div class="danger-action-title">Очистить кэш</div>
                    <div class="danger-action-desc">Удалит все кэшированные данные</div>
                </div>
                <button class="btn btn-secondary" id="clear-cache-btn">Очистить</button>
            </div>

            <div class="danger-action">
                <div>
                    <div class="danger-action-title">Сбросить статистику</div>
                    <div class="danger-action-desc">Удалит историю запросов и метрики</div>
                </div>
                <button class="btn btn-secondary" id="reset-stats-btn" style="color: var(--danger);">Сбросить</button>
            </div>

            <div class="danger-action">
                <div>
                    <div class="danger-action-title">Удалить все данные</div>
                    <div class="danger-action-desc">Полное удаление всех данных системы</div>
                </div>
                <button class="btn" id="delete-all-btn" style="background: var(--danger); color: white;">Удалить всё</button>
            </div>
        </div>
    </div>
</div>

<style>
.settings-section {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 24px;
    margin-bottom: 32px;
}

.settings-card {
    background: var(--bg-card);
    border: 1px solid var(--border-subtle);
    border-radius: var(--radius-xl);
    padding: 28px;
}

.settings-title {
    font-size: 18px;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 8px;
}

.settings-description {
    font-size: 14px;
    color: var(--text-tertiary);
    margin-bottom: 24px;
}

.api-keys-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.api-key-item {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 16px;
    background: rgba(255, 255, 255, 0.03);
    border-radius: var(--radius-md);
}

.api-key-info { flex: 1; }
.api-key-name { font-size: 14px; font-weight: 500; color: var(--text-primary); margin-bottom: 4px; }
.api-key-value { font-family: 'JetBrains Mono', monospace; font-size: 12px; color: var(--text-tertiary); }
.api-key-status { padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; }
.api-key-status.active { background: var(--success-bg); color: var(--success); }
.api-key-status.inactive { background: rgba(255, 255, 255, 0.1); color: var(--text-tertiary); }
.api-key-actions { display: flex; gap: 8px; }

.danger-zone .settings-section { grid-template-columns: 1fr; }
.danger-actions { display: flex; flex-direction: column; gap: 16px; }
.danger-action { display: flex; align-items: center; justify-content: space-between; padding: 16px; background: rgba(239, 68, 68, 0.05); border-radius: var(--radius-md); }
.danger-action-title { font-size: 14px; font-weight: 500; color: var(--text-primary); margin-bottom: 4px; }
.danger-action-desc { font-size: 13px; color: var(--text-tertiary); }

@media (max-width: 768px) { .settings-section { grid-template-columns: 1fr; } }

.tab-content { display: none; }
.tab-content.active { display: grid; }

/* Toast */
.toast {
    position: fixed;
    bottom: 24px;
    right: 24px;
    padding: 16px 24px;
    border-radius: var(--radius-md);
    font-size: 14px;
    font-weight: 500;
    z-index: 1000;
    transform: translateY(100px);
    opacity: 0;
    transition: all 0.3s ease;
}
.toast.show { transform: translateY(0); opacity: 1; }
.toast.success { background: var(--success); color: white; }
.toast.error { background: var(--danger); color: white; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tabs = document.querySelectorAll('#settings-tabs .tab');
    const contents = document.querySelectorAll('.tab-content');

    // Tab switching
    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const targetId = 'tab-' + this.dataset.tab;
            tabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            contents.forEach(content => {
                content.classList.toggle('active', content.id === targetId);
            });
        });
    });

    // Toast notification
    function showToast(message, type = 'success') {
        const toast = document.getElementById('toast');
        toast.textContent = message;
        toast.className = 'toast ' + type + ' show';
        setTimeout(() => { toast.classList.remove('show'); }, 3000);
    }

    // General settings form
    document.getElementById('general-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        const data = {
            organization_name: document.getElementById('organization_name').value,
            admin_email: document.getElementById('admin_email').value,
            timezone: document.getElementById('timezone').value,
            language: document.getElementById('language').value
        };
        try {
            const resp = await fetch('/api/settings/general', {
                method: 'POST', headers: {'X-CSRF-TOKEN': window._csrfToken, 
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            const result = await resp.json();
            if (result.success) {
                showToast(result.message, 'success');
            } else {
                showToast(result.error || 'Ошибка сохранения', 'error');
            }
        } catch (err) {
            showToast('Ошибка сети', 'error');
        }
    });

    // Monitoring settings form
    document.getElementById('monitoring-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        const data = {
            check_frequency: document.getElementById('check_frequency').value,
            min_score_threshold: document.getElementById('min_score_threshold').value,
            rating_change_notify: document.getElementById('rating_change_notify').value
        };
        try {
            const resp = await fetch('/api/settings/monitoring', {
                method: 'POST', headers: {'X-CSRF-TOKEN': window._csrfToken, 
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            const result = await resp.json();
            if (result.success) {
                showToast(result.message, 'success');
            } else {
                showToast(result.error || 'Ошибка сохранения', 'error');
            }
        } catch (err) {
            showToast('Ошибка сети', 'error');
        }
    });

    // Clear cache
    document.getElementById('clear-cache-btn').addEventListener('click', async function() {
        if (!confirm('Вы уверены, что хотите очистить кэш?')) return;
        try {
            const resp = await fetch('/api/settings/clear-cache', { method: 'POST', headers: {'X-CSRF-TOKEN': window._csrfToken} });
            const result = await resp.json();
            if (result.success) {
                showToast(result.message, 'success');
            } else {
                showToast(result.error || 'Ошибка', 'error');
            }
        } catch (err) {
            showToast('Ошибка сети', 'error');
        }
    });

    // Add API key toggle
    document.getElementById('add-api-key-btn').addEventListener('click', function() {
        document.getElementById('add-api-key-form').style.display = 'block';
        this.style.display = 'none';
    });

    document.getElementById('cancel-api-key').addEventListener('click', function() {
        document.getElementById('add-api-key-form').style.display = 'none';
        document.getElementById('add-api-key-btn').style.display = '';
    });

    // Add API key form
    document.getElementById('api-key-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        const data = {
            provider: document.getElementById('api_provider').value,
            name: document.getElementById('api_name').value,
            api_key: document.getElementById('api_key_value').value
        };
        try {
            const resp = await fetch('/api/settings/api-keys', {
                method: 'POST', headers: {'X-CSRF-TOKEN': window._csrfToken, 
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            const result = await resp.json();
            if (result.success) {
                showToast(result.message, 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                showToast(result.error || 'Ошибка', 'error');
            }
        } catch (err) {
            showToast('Ошибка сети', 'error');
        }
    });

    // Delete API key
    document.querySelectorAll('.delete-api-key').forEach(btn => {
        btn.addEventListener('click', async function() {
            if (!confirm('Удалить этот API ключ?')) return;
            const item = this.closest('.api-key-item');
            const id = item.dataset.id;
            try {
                const resp = await fetch('/api/settings/api-keys/' + id, { method: 'DELETE', headers: {'X-CSRF-TOKEN': window._csrfToken} });
                const result = await resp.json();
                if (result.success) {
                    showToast(result.message, 'success');
                    item.remove();
                } else {
                    showToast(result.error || 'Ошибка', 'error');
                }
            } catch (err) {
                showToast('Ошибка сети', 'error');
            }
        });
    });

    // Security form - Change password
    document.getElementById('security-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        const currentPassword = document.getElementById('current_password').value;
        const newPassword = document.getElementById('new_password').value;
        const confirmPassword = document.getElementById('confirm_password').value;

        if (!currentPassword || !newPassword || !confirmPassword) {
            showToast('Заполните все поля', 'error');
            return;
        }
        if (newPassword !== confirmPassword) {
            showToast('Пароли не совпадают', 'error');
            return;
        }
        if (newPassword.length < 6) {
            showToast('Пароль должен быть минимум 6 символов', 'error');
            return;
        }

        try {
            const resp = await fetch('/api/settings/change-password', {
                method: 'POST', headers: {'X-CSRF-TOKEN': window._csrfToken, 
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    current_password: currentPassword,
                    new_password: newPassword
                })
            });
            const result = await resp.json();
            if (result.success) {
                showToast(result.message, 'success');
                this.reset();
            } else {
                showToast(result.error || 'Ошибка смены пароля', 'error');
            }
        } catch (err) {
            showToast('Ошибка сети', 'error');
        }
    });

    // Reset statistics
    document.getElementById('reset-stats-btn').addEventListener('click', async function() {
        if (!confirm('Вы уверены? Это удалит всю историю запросов и метрики. Действие необратимо!')) return;
        if (!confirm('Подтвердите ещё раз: сбросить ВСЮ статистику?')) return;

        try {
            const resp = await fetch('/api/settings/reset-statistics', { method: 'POST', headers: {'X-CSRF-TOKEN': window._csrfToken} });
            const result = await resp.json();
            if (result.success) {
                showToast(result.message, 'success');
            } else {
                showToast(result.error || 'Ошибка', 'error');
            }
        } catch (err) {
            showToast('Ошибка сети', 'error');
        }
    });

    // Delete all data
    document.getElementById('delete-all-btn').addEventListener('click', async function() {
        const confirmText = prompt('Для подтверждения введите "УДАЛИТЬ ВСЁ":');
        if (confirmText !== 'УДАЛИТЬ ВСЁ') {
            showToast('Отменено. Текст не совпадает.', 'warning');
            return;
        }

        try {
            const resp = await fetch('/api/settings/delete-all-data', { method: 'POST', headers: {'X-CSRF-TOKEN': window._csrfToken} });
            const result = await resp.json();
            if (result.success) {
                showToast(result.message, 'success');
                setTimeout(() => location.href = '/', 2000);
            } else {
                showToast(result.error || 'Ошибка', 'error');
            }
        } catch (err) {
            showToast('Ошибка сети', 'error');
        }
    });
});
</script>
