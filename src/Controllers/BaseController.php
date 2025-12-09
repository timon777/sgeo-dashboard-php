<?php

namespace App\Controllers;

abstract class BaseController
{
    protected function render(string $view, array $data = [], string $layout = 'base'): void
    {
        extract($data);

        ob_start();
        include __DIR__ . '/../Views/' . $view . '.php';
        $content = ob_get_clean();

        // If hideLayout is set, just output content without layout (for login page)
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
