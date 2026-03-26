<?php

namespace App\Controllers;

abstract class BaseController
{
    public function __construct()
    {
        $this->checkSessionTimeout();
    }

    protected function checkSessionTimeout(): void
    {
        if (!isset($_SESSION['user_id'])) {
            return;
        }

        // Check inactivity timeout (15 minutes)
        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > 900) {
            $this->destroySessionAndRedirect();
            return;
        }

        // Check max session lifetime (10 hours)
        if (isset($_SESSION['created_at']) && (time() - $_SESSION['created_at']) > 36000) {
            $this->destroySessionAndRedirect();
            return;
        }

        // Force password change - block all navigation except change-password and logout
        if (!empty($_SESSION['force_password_change'])) {
            $uri = $_SERVER['REQUEST_URI'] ?? '';
            if (strpos($uri, '/settings') === false && strpos($uri, '/logout') === false) {
                header('Location: /settings');
                exit;
            }
        }

        // Update last activity
        $_SESSION['last_activity'] = time();
    }

    private function destroySessionAndRedirect(): void
    {
        session_destroy();
        header('Location: /login');
        exit;
    }

    protected function render(string $view, array $data = [], string $layout = 'base'): void
    {
        extract($data);

        ob_start();
        include __DIR__ . '/../Views/' . $view . '.php';
        $content = ob_get_clean();

        if (!empty($hideLayout)) {
            echo $content;
            return;
        }

        include __DIR__ . '/../Views/layouts/' . $layout . '.php';
    }

    protected function json(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
    }

    protected function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }
}
