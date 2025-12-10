<?php
/**
 * SGEO Dashboard - Main Entry Point
 */

session_start();

// Autoloader
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $baseDir = __DIR__ . '/../src/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

// Load environment variables from .env file
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            list($name, $value) = explode('=', $line, 2);
            putenv(trim($name) . '=' . trim($value));
        }
    }
}

// Simple Router
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Static files handling
$staticExtensions = ['css', 'js', 'png', 'jpg', 'jpeg', 'gif', 'svg', 'ico', 'woff', 'woff2'];
$extension = pathinfo($requestUri, PATHINFO_EXTENSION);
if (in_array($extension, $staticExtensions)) {
    return false;
}

// Public routes (no auth required)
$publicRoutes = [
    '/login',
    '/logout',
];

// Check authentication for protected routes
$isAuthenticated = isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
$isPublicRoute = in_array($requestUri, $publicRoutes);

if (!$isAuthenticated && !$isPublicRoute) {
    // Redirect to login page
    header('Location: /login');
    exit;
}

// Routes
$routes = [
    'GET' => [
        // Pages
        '/' => ['App\\Controllers\\DashboardController', 'index'],
        '/projects' => ['App\\Controllers\\ProjectsController', 'index'],
        '/projects/create' => ['App\\Controllers\\ProjectsController', 'create'],
        '/projects/{id}' => ['App\\Controllers\\ProjectsController', 'show'],
        '/topics/{id}' => ['App\\Controllers\\TopicController', 'show'],
        '/prompts' => ['App\\Controllers\\PromptsController', 'index'],
        '/prompts/{id}' => ['App\\Controllers\\PromptsController', 'show'],
        '/responses' => ['App\\Controllers\\ResponsesController', 'index'],
        '/sources' => ['App\\Controllers\\SourcesController', 'index'],
        '/trends' => ['App\\Controllers\\TrendsController', 'index'],
        '/reports' => ['App\\Controllers\\ReportsController', 'index'],
        '/settings' => ['App\\Controllers\\SettingsController', 'index'],

        // Auth pages
        '/login' => ['App\\Controllers\\AuthController', 'loginForm'],
        '/logout' => ['App\\Controllers\\AuthController', 'logout'],

        // API endpoints
        '/api/projects' => ['App\\Controllers\\ProjectsController', 'apiList'],
        '/api/projects/{id}' => ['App\\Controllers\\ProjectsController', 'apiShow'],
        '/api/prompts' => ['App\\Controllers\\PromptsController', 'apiList'],
        '/api/prompts/{id}' => ['App\\Controllers\\PromptsController', 'getDetail'],
        '/api/responses' => ['App\\Controllers\\ResponsesController', 'apiList'],
        '/api/sources' => ['App\\Controllers\\SourcesController', 'apiList'],
        '/api/trends/data' => ['App\\Controllers\\TrendsController', 'apiChartData'],
        '/api/reports/export/{id}' => ['App\\Controllers\\ReportsController', 'export'],
        '/api/topics/{id}/data' => ['App\\Controllers\\TopicController', 'apiTabData'],

        // Export endpoints
        '/export/projects' => ['App\\Controllers\\ProjectsController', 'exportCsv'],
        '/export/prompts' => ['App\\Controllers\\PromptsController', 'exportCsv'],
        '/export/sources' => ['App\\Controllers\\SourcesController', 'exportCsv'],
        '/export/responses' => ['App\\Controllers\\ResponsesController', 'exportCsv'],
    ],
    'POST' => [
        // Auth
        '/login' => ['App\\Controllers\\AuthController', 'login'],

        // Settings
        '/api/settings/general' => ['App\\Controllers\\SettingsController', 'saveGeneral'],
        '/api/settings/monitoring' => ['App\\Controllers\\SettingsController', 'saveMonitoring'],
        '/api/settings/clear-cache' => ['App\\Controllers\\SettingsController', 'clearCache'],
        '/api/settings/change-password' => ['App\\Controllers\\SettingsController', 'changePassword'],
        '/api/settings/reset-statistics' => ['App\\Controllers\\SettingsController', 'resetStatistics'],
        '/api/settings/delete-all-data' => ['App\\Controllers\\SettingsController', 'deleteAllData'],
        '/api/settings/api-keys' => ['App\\Controllers\\SettingsController', 'createApiKey'],

        // API CRUD
        '/api/projects' => ['App\\Controllers\\ProjectsController', 'store'],
        '/api/prompts' => ['App\\Controllers\\PromptsController', 'store'],
        '/api/sources' => ['App\\Controllers\\SourcesController', 'store'],
        '/api/sources/import' => ['App\\Controllers\\SourcesController', 'import'],
        '/api/reports' => ['App\\Controllers\\ReportsController', 'store'],
    ],
    'PUT' => [
        '/api/projects/{id}' => ['App\\Controllers\\ProjectsController', 'update'],
        '/api/prompts/{id}' => ['App\\Controllers\\PromptsController', 'update'],
        '/api/sources/{id}' => ['App\\Controllers\\SourcesController', 'update'],
    ],
    'PATCH' => [
        '/api/projects/{id}' => ['App\\Controllers\\ProjectsController', 'update'],
        '/api/prompts/{id}' => ['App\\Controllers\\PromptsController', 'update'],
        '/api/sources/{id}' => ['App\\Controllers\\SourcesController', 'update'],
    ],
    'DELETE' => [
        '/api/projects/{id}' => ['App\\Controllers\\ProjectsController', 'destroy'],
        '/api/prompts/{id}' => ['App\\Controllers\\PromptsController', 'delete'],
        '/api/sources/{id}' => ['App\\Controllers\\SourcesController', 'destroy'],
        '/api/settings/api-keys/{id}' => ['App\\Controllers\\SettingsController', 'deleteApiKey'],
    ],
];

// Match route
$handler = null;
$params = [];

if (isset($routes[$requestMethod][$requestUri])) {
    $handler = $routes[$requestMethod][$requestUri];
} else {
    // Check for dynamic routes with parameters
    foreach ($routes[$requestMethod] ?? [] as $pattern => $routeHandler) {
        // Convert route pattern to regex (e.g., /projects/{id} -> /projects/([^/]+))
        $regex = preg_replace('/\{([^}]+)\}/', '([^/]+)', $pattern);
        $regex = '#^' . $regex . '$#';

        if (preg_match($regex, $requestUri, $matches)) {
            $handler = $routeHandler;
            array_shift($matches); // Remove full match
            $params = $matches;
            break;
        }
    }
}

if ($handler) {
    [$controllerClass, $method] = $handler;
    if (class_exists($controllerClass)) {
        $controller = new $controllerClass();
        $controller->$method(...$params);
    } else {
        http_response_code(500);
        echo "Controller not found: $controllerClass";
    }
} else {
    // 404 Not Found
    http_response_code(404);
    include __DIR__ . '/../src/Views/errors/404.php';
}
