<?php

namespace App\Controllers;

use App\Services\SupabaseClient;

class AuthController extends BaseController
{
    public function loginForm(): void
    {
        // If already logged in, redirect to dashboard
        if ($this->isAuthenticated()) {
            header('Location: /');
            exit;
        }

        $this->render('auth/login', [
            'pageTitle' => 'Вход в систему',
            'hideLayout' => true,
        ]);
    }

    public function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            return;
        }

        $login = $_POST['login'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($login) || empty($password)) {
            $this->render('auth/login', [
                'pageTitle' => 'Вход в систему',
                'hideLayout' => true,
                'error' => 'Введите логин и пароль',
            ]);
            return;
        }

        $db = new SupabaseClient();

        // Find user by login
        $result = $db->from('users')
            ->select('*')
            ->eq('login', $login)
            ->eq('is_active', 'true')
            ->single();

        if (!$result || !password_verify($password, $result['password_hash'])) {
            $this->render('auth/login', [
                'pageTitle' => 'Вход в систему',
                'hideLayout' => true,
                'error' => 'Неверный логин или пароль',
            ]);
            return;
        }

        // Create session
        $token = bin2hex(random_bytes(32));
        $expiresAt = date('c', strtotime('+24 hours'));

        $db->from('user_sessions')->insert([
            'user_id' => $result['id'],
            'token' => $token,
            'expires_at' => $expiresAt,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
        ]);

        // Update last login
        $db->from('users')
            ->eq('id', $result['id'])
            ->update(['last_login' => date('c')]);

        // Set session
        $_SESSION['user_id'] = $result['id'];
        $_SESSION['user_name'] = $result['name'];
        $_SESSION['user_email'] = $result['email'];
        $_SESSION['user_role'] = $result['role'];
        $_SESSION['user_avatar'] = $result['avatar_initials'];
        $_SESSION['auth_token'] = $token;

        // Redirect to dashboard
        header('Location: /');
        exit;
    }

    public function logout(): void
    {
        $token = $_SESSION['auth_token'] ?? null;

        if ($token) {
            $db = new SupabaseClient();
            $db->from('user_sessions')
                ->eq('token', $token)
                ->delete();
        }

        // Clear session
        session_destroy();

        header('Location: /login');
        exit;
    }

    public function isAuthenticated(): bool
    {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }

    public function getCurrentUser(): ?array
    {
        if (!$this->isAuthenticated()) {
            return null;
        }

        return [
            'id' => $_SESSION['user_id'],
            'name' => $_SESSION['user_name'],
            'email' => $_SESSION['user_email'],
            'role' => $_SESSION['user_role'],
            'avatar' => $_SESSION['user_avatar'],
        ];
    }

    public function requireAuth(): void
    {
        if (!$this->isAuthenticated()) {
            header('Location: /login');
            exit;
        }
    }

    public function requireRole(string $role): void
    {
        $this->requireAuth();

        $userRole = $_SESSION['user_role'] ?? 'user';
        $roleHierarchy = ['admin' => 3, 'manager' => 2, 'user' => 1];

        $requiredLevel = $roleHierarchy[$role] ?? 1;
        $userLevel = $roleHierarchy[$userRole] ?? 1;

        if ($userLevel < $requiredLevel) {
            http_response_code(403);
            include __DIR__ . '/../Views/errors/403.php';
            exit;
        }
    }
}
