#!/usr/bin/env php
<?php
/**
 * SGEO Dashboard - Link AI Responses to Projects
 *
 * This script links ai_responses to projects based on prompt content.
 * Run from project root: php database/link_responses.php
 */

// Load environment variables
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            putenv(trim($line));
        }
    }
}

require_once __DIR__ . '/../src/Services/SupabaseClient.php';
require_once __DIR__ . '/../src/Models/Project.php';
require_once __DIR__ . '/../src/Models/AiResponse.php';

use App\Services\SupabaseClient;
use App\Models\Project;
use App\Models\AiResponse;

echo "=== SGEO: Linking AI Responses to Projects ===\n\n";

// Load prompt -> theme mapping from CSV
$csvFile = __DIR__ . '/../project_source/promt.csv';
if (!file_exists($csvFile)) {
    die("Error: promt.csv not found\n");
}

$promptToTheme = [];
$handle = fopen($csvFile, 'r');
$header = fgetcsv($handle, 0, ';'); // Skip header

while (($row = fgetcsv($handle, 0, ';')) !== false) {
    if (!empty($row[0]) && !empty($row[1])) {
        $theme = trim($row[0]);
        $prompt = trim($row[1]);
        // Store prompt without "Cite all sources..." suffix
        $promptToTheme[$prompt] = $theme;
    }
}
fclose($handle);

echo "Loaded " . count($promptToTheme) . " prompts from CSV\n";

// Get projects
$projectModel = new Project();
$projectsResult = $projectModel->all(false);
$projects = $projectsResult['data'] ?? [];

echo "Found " . count($projects) . " projects in database\n\n";

// Create theme -> project_id mapping
$themeToProjectId = [];
foreach ($projects as $project) {
    $name = $project['name'];
    $id = $project['id'];

    // Map CSV themes to project names (handling slight differences)
    $themeToProjectId['Freedom Bank'] = ($name === 'Freedom Bank') ? $id : ($themeToProjectId['Freedom Bank'] ?? null);
    $themeToProjectId['Freedom Broker'] = ($name === 'Freedom Broker') ? $id : ($themeToProjectId['Freedom Broker'] ?? null);
    $themeToProjectId['Имидж Президента Токаева'] = ($name === 'Имидж Президента Токаева') ? $id : ($themeToProjectId['Имидж Президента Токаева'] ?? null);
    $themeToProjectId['Январские события 2022 года'] = ($name === 'Январские события 2022 года') ? $id : ($themeToProjectId['Январские события 2022 года'] ?? null);
    $themeToProjectId['Цифровой Казахстан'] = ($name === 'Цифровой Казахстан') ? $id : ($themeToProjectId['Цифровой Казахстан'] ?? null);
    $themeToProjectId['АЭС и ядерная энергетика'] = ($name === 'АЭС и ядерная энергетика') ? $id : ($themeToProjectId['АЭС и ядерная энергетика'] ?? null);

    // Handle "Идеология «Закон и порядок»" -> "Идеология, закон и порядок"
    if (stripos($name, 'закон') !== false && stripos($name, 'порядок') !== false) {
        $themeToProjectId['Идеология «Закон и порядок»'] = $id;
    }
}

echo "Theme to Project ID mapping:\n";
foreach ($themeToProjectId as $theme => $projectId) {
    echo "  - $theme: " . ($projectId ?? 'NOT FOUND') . "\n";
}
echo "\n";

// Get all ai_responses
$db = new SupabaseClient();
$responsesResult = $db->from('ai_responses')
    ->select('id,prompt,project_id')
    ->limit(1000)
    ->get();

$responses = $responsesResult['data'] ?? [];
echo "Found " . count($responses) . " AI responses to process\n\n";

$updated = 0;
$skipped = 0;
$notMatched = 0;

foreach ($responses as $response) {
    $responseId = $response['id'];
    $promptText = $response['prompt'];
    $currentProjectId = $response['project_id'];

    // Clean prompt for matching (remove "Cite all sources..." suffix)
    $cleanPrompt = preg_replace('/\s*Cite all sources.*$/i', '', $promptText);
    $cleanPrompt = trim($cleanPrompt);

    // Find matching theme
    $matchedTheme = null;
    foreach ($promptToTheme as $csvPrompt => $theme) {
        if (stripos($cleanPrompt, $csvPrompt) !== false || stripos($csvPrompt, $cleanPrompt) !== false) {
            $matchedTheme = $theme;
            break;
        }
    }

    if (!$matchedTheme) {
        // Try keyword matching
        if (stripos($promptText, 'Freedom Bank') !== false) {
            $matchedTheme = 'Freedom Bank';
        } elseif (stripos($promptText, 'Freedom Broker') !== false) {
            $matchedTheme = 'Freedom Broker';
        } elseif (stripos($promptText, 'Токаев') !== false || stripos($promptText, 'Тоқаев') !== false || stripos($promptText, 'Tokayev') !== false) {
            $matchedTheme = 'Имидж Президента Токаева';
        } elseif (stripos($promptText, 'январ') !== false || stripos($promptText, 'Қаңтар') !== false || stripos($promptText, 'January 2022') !== false) {
            $matchedTheme = 'Январские события 2022 года';
        } elseif (stripos($promptText, 'Закон и порядок') !== false || stripos($promptText, 'Law-and-Order') !== false || stripos($promptText, 'Заң және тәртіп') !== false) {
            $matchedTheme = 'Идеология «Закон и порядок»';
        } elseif (stripos($promptText, 'Цифров') !== false || stripos($promptText, 'Цифрлық') !== false || stripos($promptText, 'Digital') !== false) {
            $matchedTheme = 'Цифровой Казахстан';
        } elseif (stripos($promptText, 'АЭС') !== false || stripos($promptText, 'атом') !== false || stripos($promptText, 'nuclear') !== false || stripos($promptText, 'referendum') !== false) {
            $matchedTheme = 'АЭС и ядерная энергетика';
        }
    }

    if ($matchedTheme && isset($themeToProjectId[$matchedTheme])) {
        $newProjectId = $themeToProjectId[$matchedTheme];

        if ($currentProjectId === $newProjectId) {
            $skipped++;
            continue;
        }

        // Update the response
        $updateResult = $db->from('ai_responses')
            ->eq('id', $responseId)
            ->update(['project_id' => $newProjectId]);

        if (isset($updateResult['error'])) {
            echo "Error updating $responseId: " . json_encode($updateResult['error']) . "\n";
        } else {
            $updated++;
            echo "Linked: $matchedTheme -> " . substr($promptText, 0, 50) . "...\n";
        }
    } else {
        $notMatched++;
        echo "No match: " . substr($promptText, 0, 60) . "...\n";
    }
}

echo "\n=== Summary ===\n";
echo "Updated: $updated\n";
echo "Already linked (skipped): $skipped\n";
echo "Not matched: $notMatched\n";
echo "\nDone!\n";
