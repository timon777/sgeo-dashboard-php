<?php

namespace App\Controllers;

abstract class BaseController
{
    protected function render(string $view, array $data = []): void
    {
        extract($data);

        ob_start();
        include __DIR__ . '/../Views/' . $view . '.php';
        $content = ob_get_clean();

        include __DIR__ . '/../Views/layouts/base.php';
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
