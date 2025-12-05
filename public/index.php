<?php
/**
 * SGEO Dashboard - Main Entry Point
 */

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

// Routes
$routes = [
    'GET' => [
        '/' => ['App\\Controllers\\DashboardController', 'index'],
        '/projects' => ['App\\Controllers\\ProjectsController', 'index'],
        '/projects/{id}' => ['App\\Controllers\\ProjectsController', 'show'],
        '/prompts' => ['App\\Controllers\\PromptsController', 'index'],
        '/sources' => ['App\\Controllers\\SourcesController', 'index'],
        '/llm-monitoring' => ['App\\Controllers\\LlmMonitoringController', 'index'],
        '/trends' => ['App\\Controllers\\TrendsController', 'index'],
        '/reports' => ['App\\Controllers\\ReportsController', 'index'],
        '/settings' => ['App\\Controllers\\SettingsController', 'index'],
    ],
    'POST' => [
        '/api/prompts' => ['App\\Controllers\\Api\\PromptsApiController', 'store'],
        '/api/evaluations' => ['App\\Controllers\\Api\\EvaluationsApiController', 'store'],
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
