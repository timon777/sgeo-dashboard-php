<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход — SGEO Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/main.css">
    <style>
        .login-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            width: 100%;
            max-width: 420px;
        }

        .login-card {
            background: var(--bg-card);
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-xl);
            padding: 40px;
        }

        .login-header {
            text-align: center;
            margin-bottom: 32px;
        }

        .login-logo {
            width: 64px;
            height: 64px;
            background: var(--accent-gradient);
            border-radius: var(--radius-lg);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            box-shadow: 0 8px 32px rgba(99, 102, 241, 0.3);
        }

        .login-logo span {
            font-size: 28px;
            font-weight: 800;
            color: white;
        }

        .login-title {
            font-size: 24px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 8px;
        }

        .login-subtitle {
            font-size: 14px;
            color: var(--text-tertiary);
        }

        .login-form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .form-label {
            font-size: 13px;
            font-weight: 500;
            color: var(--text-secondary);
        }

        .form-input {
            width: 100%;
            padding: 14px 16px;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-md);
            color: var(--text-primary);
            font-size: 15px;
            font-family: inherit;
            transition: all 0.2s ease;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--accent-primary);
            background: rgba(99, 102, 241, 0.05);
        }

        .form-input::placeholder {
            color: var(--text-muted);
        }

        .error-message {
            background: var(--danger-bg);
            border: 1px solid rgba(239, 68, 68, 0.2);
            color: var(--danger);
            padding: 12px 16px;
            border-radius: var(--radius-md);
            font-size: 14px;
            text-align: center;
        }

        .login-btn {
            width: 100%;
            padding: 14px 24px;
            background: var(--accent-gradient);
            border: none;
            border-radius: var(--radius-md);
            color: white;
            font-size: 15px;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 4px 16px rgba(99, 102, 241, 0.3);
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 24px rgba(99, 102, 241, 0.4);
        }

        .login-footer {
            text-align: center;
            margin-top: 24px;
            padding-top: 24px;
            border-top: 1px solid var(--border-subtle);
        }

        .login-footer-text {
            font-size: 13px;
            color: var(--text-tertiary);
        }

        .remember-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .remember-label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: var(--text-secondary);
            cursor: pointer;
        }

        .remember-label input {
            width: 16px;
            height: 16px;
            accent-color: var(--accent-primary);
        }

        .forgot-link {
            font-size: 13px;
            color: var(--accent-primary);
            text-decoration: none;
        }

        .forgot-link:hover {
            text-decoration: underline;
        }

        .login-btn {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .login-btn.loading {
            pointer-events: none;
            opacity: 0.85;
        }

        .login-btn .btn-text {
            transition: opacity 0.2s;
        }

        .login-btn.loading .btn-text {
            opacity: 0.7;
        }

        .login-btn .spinner {
            display: none;
            width: 18px;
            height: 18px;
            animation: spin 0.8s linear infinite;
        }

        .login-btn.loading .spinner {
            display: block;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="bg-effects">
        <div class="bg-gradient-orb"></div>
        <div class="bg-gradient-orb"></div>
        <div class="bg-noise"></div>
    </div>

    <div class="login-page">
        <div class="login-container">
            <div class="login-card">
                <div class="login-header">
                    <div class="login-logo">
                        <span>S</span>
                    </div>
                    <h1 class="login-title">Вход в SGEO</h1>
                </div>

                <?php if (!empty($error)): ?>
                <div class="error-message">
                    <?= htmlspecialchars($error) ?>
                </div>
                <?php endif; ?>

                <form class="login-form" method="POST" action="/login">
                    <?= AppServicesCsrf::field() ?>
                    <div class="form-group">
                        <label class="form-label" for="login">Логин</label>
                        <input
                            type="text"
                            id="login"
                            name="login"
                            class="form-input"
                            placeholder="admin"
                            required
                            autofocus
                        >
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="password">Пароль</label>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-input"
                            placeholder="Введите пароль"
                            required
                        >
                    </div>

                    <div class="remember-row">
                        <label class="remember-label">
                            <input type="checkbox" name="remember" value="1">
                            Запомнить меня
                        </label>
                        <a href="#" class="forgot-link">Забыли пароль?</a>
                    </div>

                    <button type="submit" class="login-btn" id="loginBtn">
                        <svg class="spinner" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <circle cx="12" cy="12" r="10" stroke-dasharray="30 60" />
                        </svg>
                        <span class="btn-text">Войти</span>
                    </button>
                </form>

                <div class="login-footer">
                    <p class="login-footer-text">SGEO Dashboard v1.0</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.querySelector('.login-form').addEventListener('submit', function(e) {
            const btn = document.getElementById('loginBtn');
            btn.classList.add('loading');
            btn.querySelector('.btn-text').textContent = 'Вход...';
        });
    </script>
</body>
</html>
