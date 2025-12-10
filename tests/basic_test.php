#!/usr/bin/env php
<?php
/**
 * Basic functionality tests for SGEO Dashboard
 */

$testsPassed = 0;
$testsFailed = 0;

function test($name, $condition) {
    global $testsPassed, $testsFailed;
    if ($condition) {
        echo "  [PASS] $name\n";
        $testsPassed++;
    } else {
        echo "  [FAIL] $name\n";
        $testsFailed++;
    }
}

echo "\n=== SGEO Dashboard Tests ===\n\n";

// Test 1: Check all required files exist
echo "1. File Structure Tests:\n";

$requiredFiles = [
    'public/index.php',
    'src/Controllers/DashboardController.php',
    'src/Controllers/ProjectsController.php',
    'src/Controllers/PromptsController.php',
    'src/Controllers/ResponsesController.php',
    'src/Controllers/SourcesController.php',
    'src/Controllers/TopicController.php',
    'src/Controllers/AuthController.php',
    'src/Views/dashboard/index.php',
    'src/Views/partials/sidebar.php',
    'src/Views/layouts/base.php',
    'public/assets/css/main.css',
    'public/assets/js/main.js',
];

foreach ($requiredFiles as $file) {
    test("File exists: $file", file_exists(__DIR__ . '/../' . $file));
}

// Test 2: Check PHP syntax in all PHP files
echo "\n2. PHP Syntax Tests:\n";

$phpFiles = glob(__DIR__ . '/../src/**/*.php') +
            glob(__DIR__ . '/../src/**/**/*.php') +
            glob(__DIR__ . '/../public/*.php');

$syntaxErrors = [];
foreach ($phpFiles as $file) {
    $output = shell_exec("php -l '$file' 2>&1");
    if (strpos($output, 'No syntax errors') === false) {
        $syntaxErrors[] = basename($file) . ': ' . trim($output);
    }
}

test("All PHP files have valid syntax", empty($syntaxErrors));
if (!empty($syntaxErrors)) {
    foreach ($syntaxErrors as $err) {
        echo "    ERROR: $err\n";
    }
}

// Test 3: Check controller classes can be loaded
echo "\n3. Controller Loading Tests:\n";

// Setup autoloader
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

$controllers = [
    'App\\Controllers\\DashboardController',
    'App\\Controllers\\ProjectsController',
    'App\\Controllers\\PromptsController',
    'App\\Controllers\\ResponsesController',
    'App\\Controllers\\SourcesController',
    'App\\Controllers\\TopicController',
];

foreach ($controllers as $controller) {
    test("Class loadable: " . basename(str_replace('\\', '/', $controller)), class_exists($controller));
}

// Test 4: Check CSS/JS files for common issues
echo "\n4. Asset Tests:\n";

$cssContent = file_get_contents(__DIR__ . '/../public/assets/css/main.css');
test("CSS has light-theme styles", strpos($cssContent, '.light-theme') !== false);
test("CSS has legend styles", strpos($cssContent, '.legend') !== false);
test("CSS has chart styles", strpos($cssContent, '.chart') !== false);

$jsContent = file_get_contents(__DIR__ . '/../public/assets/js/main.js');
test("JS has isLightTheme function", strpos($jsContent, 'isLightTheme') !== false);
test("JS has toggleTheme function", strpos($jsContent, 'toggleTheme') !== false);
test("JS has getChartColors function", strpos($jsContent, 'getChartColors') !== false);

// Test 5: Check views for theme-aware chart colors
echo "\n5. Theme-Aware Charts Tests:\n";

$dashboardView = file_get_contents(__DIR__ . '/../src/Views/dashboard/index.php');
$themeAwareChartCount = substr_count($dashboardView, "isLightTheme() ? '#1a1c22' : '#fff'");
test("Dashboard has theme-aware point colors (found: $themeAwareChartCount)", $themeAwareChartCount >= 4);

$topicsView = file_get_contents(__DIR__ . '/../src/Views/topics/show.php');
test("Topics view has theme-aware point colors", strpos($topicsView, "isLightTheme() ? '#1a1c22' : '#fff'") !== false);

// Test 6: Check sidebar menu items
echo "\n6. Sidebar Menu Tests:\n";

$sidebarContent = file_get_contents(__DIR__ . '/../src/Views/partials/sidebar.php');
test("Sidebar has Dashboard link", strpos($sidebarContent, 'href="/"') !== false);
test("Sidebar has Projects link", strpos($sidebarContent, 'href="/projects"') !== false);
test("Sidebar has Prompts link", strpos($sidebarContent, 'href="/prompts"') !== false);
test("Sidebar has Responses link", strpos($sidebarContent, 'href="/responses"') !== false);
test("Sidebar has Sources link", strpos($sidebarContent, 'href="/sources"') !== false);
test("Sidebar does NOT have Trends link", strpos($sidebarContent, 'href="/trends"') === false);
test("Sidebar does NOT have Аналитика section", strpos($sidebarContent, 'Аналитика') === false);

// Test 7: Check routes configuration
echo "\n7. Routes Tests:\n";

$indexContent = file_get_contents(__DIR__ . '/../public/index.php');
test("Route: Dashboard (/)", strpos($indexContent, "'/'" . " =>") !== false);
test("Route: Projects", strpos($indexContent, "'/projects'") !== false);
test("Route: Prompts", strpos($indexContent, "'/prompts'") !== false);
test("Route: Responses", strpos($indexContent, "'/responses'") !== false);
test("Route: Sources", strpos($indexContent, "'/sources'") !== false);
test("Route: API responses", strpos($indexContent, "'/api/responses'") !== false);

// Summary
echo "\n=== Test Summary ===\n";
echo "Passed: $testsPassed\n";
echo "Failed: $testsFailed\n";
echo "Total: " . ($testsPassed + $testsFailed) . "\n\n";

exit($testsFailed > 0 ? 1 : 0);
