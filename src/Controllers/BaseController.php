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

    /**
     * Get current user's project filter
     * Returns: 'all', 'gov', or 'private:partner_name'
     */
    protected function getProjectFilter(): string
    {
        return $_SESSION['user_project_filter'] ?? 'all';
    }

    /**
     * Check if user can access a specific project
     */
    protected function canAccessProject(array $project): bool
    {
        $filter = $this->getProjectFilter();

        if ($filter === 'all') {
            return true;
        }

        // Filter for government projects only
        if ($filter === 'gov') {
            return ($project['type'] ?? '') === 'gov';
        }

        // Filter for specific private partner (e.g., 'private:freedom')
        if (str_starts_with($filter, 'private:')) {
            $partnerName = substr($filter, 8); // Remove 'private:' prefix
            return ($project['type'] ?? '') === 'private' &&
                   stripos($project['name'] ?? '', $partnerName) !== false;
        }

        return false;
    }

    /**
     * Apply project filter to Supabase query builder
     */
    protected function applyProjectFilter($queryBuilder): mixed
    {
        $filter = $this->getProjectFilter();

        if ($filter === 'all') {
            return $queryBuilder;
        }

        // Filter for government projects only
        if ($filter === 'gov') {
            return $queryBuilder->eq('type', 'gov');
        }

        // Filter for specific private partner (e.g., 'private:freedom')
        if (str_starts_with($filter, 'private:')) {
            $partnerName = substr($filter, 8); // Remove 'private:' prefix
            return $queryBuilder->eq('type', 'private')->ilike('name', "%{$partnerName}%");
        }

        return $queryBuilder;
    }
}
