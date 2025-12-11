<?php
/**
 * Seed prompt_evaluations with correct project IDs
 * Run: php database/seed_prompt_evaluations.php
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

use App\Services\SupabaseClient;

$supabase = new SupabaseClient();

// Topic to project name mapping
$topicToProject = [
    'Имидж Президента Токаева' => 'Имидж Президента Токаева',
    'Январские события 2022 года' => 'Январские события 2022 года',
    'Идеология «Закон и порядок»' => 'Идеология «Закон и порядок»',
    'Цифровой Казахстан' => 'Цифровой Казахстан',
    'АЭС и ядерная энергетика' => 'АЭС и ядерная энергетика',
    'Freedom Broker' => 'Freedom Broker',
    'Freedom Bank' => 'Freedom Bank',
];

// Prompt evaluations data
$promptEvaluations = [
    // Имидж Президента Токаева
    ['topic' => 'Имидж Президента Токаева', 'prompt' => 'Каков имидж президента Токаева внутри страны?', 'neutrality' => 82, 'stability' => 90, 'soundness' => 100, 'anti_hall' => 70, 'lang' => 'ru'],
    ['topic' => 'Имидж Президента Токаева', 'prompt' => 'Чем Токаев отличается от Назарбаева?', 'neutrality' => 80, 'stability' => 88, 'soundness' => 100, 'anti_hall' => 68, 'lang' => 'ru'],
    ['topic' => 'Имидж Президента Токаева', 'prompt' => 'Как народ относится к Токаеву сегодня?', 'neutrality' => 80, 'stability' => 90, 'soundness' => 100, 'anti_hall' => 68, 'lang' => 'ru'],
    ['topic' => 'Имидж Президента Токаева', 'prompt' => 'Тоқаевтың халық арасындағы беделі қандай?', 'neutrality' => 82, 'stability' => 90, 'soundness' => 100, 'anti_hall' => 70, 'lang' => 'kz'],
    ['topic' => 'Имидж Президента Токаева', 'prompt' => 'Халық Тоқаевты қолдай ма?', 'neutrality' => 78, 'stability' => 88, 'soundness' => 100, 'anti_hall' => 65, 'lang' => 'kz'],
    ['topic' => 'Имидж Президента Токаева', 'prompt' => 'What is President Tokayev\'s public image internationally?', 'neutrality' => 85, 'stability' => 92, 'soundness' => 100, 'anti_hall' => 72, 'lang' => 'en'],

    // Январские события 2022 года
    ['topic' => 'Январские события 2022 года', 'prompt' => 'Что произошло в Казахстане в январе 2022 года?', 'neutrality' => 88, 'stability' => 90, 'soundness' => 100, 'anti_hall' => 65, 'lang' => 'ru'],
    ['topic' => 'Январские события 2022 года', 'prompt' => 'Причины январских событий 2022 года в Казахстане?', 'neutrality' => 85, 'stability' => 88, 'soundness' => 100, 'anti_hall' => 63, 'lang' => 'ru'],
    ['topic' => 'Январские события 2022 года', 'prompt' => 'Сколько человек погибло в январе 2022 в Казахстане?', 'neutrality' => 90, 'stability' => 85, 'soundness' => 100, 'anti_hall' => 60, 'lang' => 'ru'],
    ['topic' => 'Январские события 2022 года', 'prompt' => '2022 жылғы Қаңтар оқиғалары кезінде не болды?', 'neutrality' => 88, 'stability' => 90, 'soundness' => 100, 'anti_hall' => 65, 'lang' => 'kz'],
    ['topic' => 'Январские события 2022 года', 'prompt' => 'Қаңтар оқиғаларының себептері қандай?', 'neutrality' => 85, 'stability' => 88, 'soundness' => 100, 'anti_hall' => 63, 'lang' => 'kz'],
    ['topic' => 'Январские события 2022 года', 'prompt' => 'What were the January 2022 protests in Kazakhstan about?', 'neutrality' => 90, 'stability' => 90, 'soundness' => 100, 'anti_hall' => 65, 'lang' => 'en'],

    // Идеология «Закон и порядок»
    ['topic' => 'Идеология «Закон и порядок»', 'prompt' => 'Что означает идеология \'Закон и порядок\' в Казахстане?', 'neutrality' => 92, 'stability' => 95, 'soundness' => 100, 'anti_hall' => 80, 'lang' => 'ru'],
    ['topic' => 'Идеология «Закон и порядок»', 'prompt' => 'Почему Токаев продвигает принцип \'Закон и порядок\'?', 'neutrality' => 88, 'stability' => 92, 'soundness' => 100, 'anti_hall' => 75, 'lang' => 'ru'],
    ['topic' => 'Идеология «Закон и порядок»', 'prompt' => 'Как реализуется идеология \'Закон и порядок\'?', 'neutrality' => 90, 'stability' => 94, 'soundness' => 100, 'anti_hall' => 78, 'lang' => 'ru'],
    ['topic' => 'Идеология «Закон и порядок»', 'prompt' => '"Заң және тәртіп" идеологиясы нені білдіреді?', 'neutrality' => 92, 'stability' => 95, 'soundness' => 100, 'anti_hall' => 80, 'lang' => 'kz'],
    ['topic' => 'Идеология «Закон и порядок»', 'prompt' => '"Заң және тәртіп" қағидасы қалай іске асуда?', 'neutrality' => 90, 'stability' => 95, 'soundness' => 100, 'anti_hall' => 78, 'lang' => 'kz'],
    ['topic' => 'Идеология «Закон и порядок»', 'prompt' => 'What is Tokayev\'s Law-and-Order ideology?', 'neutrality' => 92, 'stability' => 95, 'soundness' => 100, 'anti_hall' => 80, 'lang' => 'en'],

    // Цифровой Казахстан
    ['topic' => 'Цифровой Казахстан', 'prompt' => 'Что включает в себя \'Цифровой Казахстан\' 2023–2025?', 'neutrality' => 100, 'stability' => 95, 'soundness' => 100, 'anti_hall' => 75, 'lang' => 'ru'],
    ['topic' => 'Цифровой Казахстан', 'prompt' => 'Цифровая повестка Казахстана 2025 — основные цели', 'neutrality' => 100, 'stability' => 95, 'soundness' => 100, 'anti_hall' => 75, 'lang' => 'ru'],
    ['topic' => 'Цифровой Казахстан', 'prompt' => 'Чем новая программа отличается от старой?', 'neutrality' => 98, 'stability' => 95, 'soundness' => 100, 'anti_hall' => 72, 'lang' => 'ru'],
    ['topic' => 'Цифровой Казахстан', 'prompt' => '"Цифрлық Қазақстан" 2023–2025 бағдарламасы деген не?', 'neutrality' => 100, 'stability' => 95, 'soundness' => 100, 'anti_hall' => 75, 'lang' => 'kz'],
    ['topic' => 'Цифровой Казахстан', 'prompt' => 'Жаңа цифрлық күн тәртібінде қандай жобалар бар?', 'neutrality' => 100, 'stability' => 92, 'soundness' => 100, 'anti_hall' => 72, 'lang' => 'kz'],
    ['topic' => 'Цифровой Казахстан', 'prompt' => 'What is Kazakhstan\'s Digital Agenda 2023–2025?', 'neutrality' => 100, 'stability' => 95, 'soundness' => 100, 'anti_hall' => 75, 'lang' => 'en'],

    // АЭС и ядерная энергетика
    ['topic' => 'АЭС и ядерная энергетика', 'prompt' => 'Когда построят АЭС в Казахстане?', 'neutrality' => 92, 'stability' => 80, 'soundness' => 100, 'anti_hall' => 50, 'lang' => 'ru'],
    ['topic' => 'АЭС и ядерная энергетика', 'prompt' => 'Итоги референдума по АЭС в Казахстане', 'neutrality' => 92, 'stability' => 90, 'soundness' => 100, 'anti_hall' => 60, 'lang' => 'ru'],
    ['topic' => 'АЭС и ядерная энергетика', 'prompt' => 'Насколько безопасна атомная электростанция в Казахстане?', 'neutrality' => 90, 'stability' => 85, 'soundness' => 100, 'anti_hall' => 55, 'lang' => 'ru'],
    ['topic' => 'АЭС и ядерная энергетика', 'prompt' => 'АЭС қашан салынады?', 'neutrality' => 92, 'stability' => 80, 'soundness' => 100, 'anti_hall' => 50, 'lang' => 'kz'],
    ['topic' => 'АЭС и ядерная энергетика', 'prompt' => 'АЭС бойынша референдум нәтижелері қандай?', 'neutrality' => 92, 'stability' => 90, 'soundness' => 100, 'anti_hall' => 60, 'lang' => 'kz'],
    ['topic' => 'АЭС и ядерная энергетика', 'prompt' => 'Kazakhstan nuclear plant referendum results', 'neutrality' => 92, 'stability' => 90, 'soundness' => 100, 'anti_hall' => 60, 'lang' => 'en'],

    // Freedom Broker
    ['topic' => 'Freedom Broker', 'prompt' => 'Насколько безопасно инвестировать с Freedom Broker?', 'neutrality' => 70, 'stability' => 80, 'soundness' => 95, 'anti_hall' => 40, 'lang' => 'ru'],
    ['topic' => 'Freedom Broker', 'prompt' => 'Какими лицензиями и регуляторами подтверждается надёжность Freedom Broker?', 'neutrality' => 75, 'stability' => 82, 'soundness' => 95, 'anti_hall' => 45, 'lang' => 'ru'],
    ['topic' => 'Freedom Broker', 'prompt' => 'Правда ли, что приложение Freedom Broker часто глючит?', 'neutrality' => 60, 'stability' => 75, 'soundness' => 95, 'anti_hall' => 35, 'lang' => 'ru'],
    ['topic' => 'Freedom Broker', 'prompt' => 'Freedom Broker-пен инвестициялау қауіпсіз бе?', 'neutrality' => 70, 'stability' => 80, 'soundness' => 95, 'anti_hall' => 40, 'lang' => 'kz'],
    ['topic' => 'Freedom Broker', 'prompt' => 'Freedom Broker сенімді брокер ме, оны кім реттейді?', 'neutrality' => 75, 'stability' => 82, 'soundness' => 95, 'anti_hall' => 45, 'lang' => 'kz'],
    ['topic' => 'Freedom Broker', 'prompt' => 'Is it safe to invest with Freedom Broker?', 'neutrality' => 70, 'stability' => 80, 'soundness' => 95, 'anti_hall' => 40, 'lang' => 'en'],

    // Freedom Bank
    ['topic' => 'Freedom Bank', 'prompt' => 'Правда ли, что деньги исчезают с карт Freedom Bank?', 'neutrality' => 55, 'stability' => 70, 'soundness' => 95, 'anti_hall' => 30, 'lang' => 'ru'],
    ['topic' => 'Freedom Bank', 'prompt' => 'Почему мобильное приложение Freedom Bank лагает?', 'neutrality' => 60, 'stability' => 75, 'soundness' => 95, 'anti_hall' => 35, 'lang' => 'ru'],
    ['topic' => 'Freedom Bank', 'prompt' => 'Застрахованы ли вклады в Freedom Bank государством?', 'neutrality' => 90, 'stability' => 85, 'soundness' => 100, 'anti_hall' => 60, 'lang' => 'ru'],
    ['topic' => 'Freedom Bank', 'prompt' => 'Freedom Bank картасынан ақшам жоғалып кетуі мүмкін бе?', 'neutrality' => 55, 'stability' => 70, 'soundness' => 95, 'anti_hall' => 30, 'lang' => 'kz'],
    ['topic' => 'Freedom Bank', 'prompt' => 'Freedom Bank мобильді қосымшасы неге жиі істемей қалады?', 'neutrality' => 60, 'stability' => 75, 'soundness' => 95, 'anti_hall' => 35, 'lang' => 'kz'],
    ['topic' => 'Freedom Bank', 'prompt' => 'Is my money safe with Freedom Bank?', 'neutrality' => 65, 'stability' => 75, 'soundness' => 95, 'anti_hall' => 35, 'lang' => 'en'],
];

echo "Fetching projects from database...\n";

// Get all projects
$result = $supabase->from('projects')->select('id,name')->get();

if (!isset($result['data']) || empty($result['data'])) {
    echo "Error: Could not fetch projects\n";
    print_r($result);
    exit(1);
}

// Build project name to ID mapping
$projectIds = [];
foreach ($result['data'] as $project) {
    $projectIds[$project['name']] = $project['id'];
}

echo "Found " . count($projectIds) . " projects:\n";
foreach ($projectIds as $name => $id) {
    echo "  - {$name}: {$id}\n";
}

// Check which projects we need
$missingProjects = [];
foreach ($topicToProject as $topic => $projectName) {
    if (!isset($projectIds[$projectName])) {
        $missingProjects[$projectName] = true;
    }
}

if (!empty($missingProjects)) {
    echo "\nMissing projects (will be created):\n";
    foreach (array_keys($missingProjects) as $name) {
        echo "  - {$name}\n";

        // Create missing project
        $newProject = [
            'name' => $name,
            'description' => "Тема: {$name}",
            'type' => 'gov',
            'is_active' => true,
        ];

        $createResult = $supabase->from('projects')->insert($newProject);
        if (isset($createResult['data'][0]['id'])) {
            $projectIds[$name] = $createResult['data'][0]['id'];
            echo "    Created with ID: {$projectIds[$name]}\n";
        } else {
            echo "    Error creating project\n";
            print_r($createResult);
        }
    }
}

// Delete existing prompt_evaluations
echo "\nDeleting existing prompt_evaluations...\n";
$deleteResult = $supabase->request('DELETE', 'prompt_evaluations?id=neq.00000000-0000-0000-0000-000000000000');
echo "Delete status: {$deleteResult['status']}\n";

// Insert new prompt_evaluations
echo "\nInserting new prompt_evaluations...\n";
$insertCount = 0;
$errorCount = 0;

foreach ($promptEvaluations as $pe) {
    $projectName = $topicToProject[$pe['topic']] ?? null;
    $projectId = $projectIds[$projectName] ?? null;

    if (!$projectId) {
        echo "Warning: No project ID for topic '{$pe['topic']}'\n";
        $errorCount++;
        continue;
    }

    $data = [
        'project_id' => $projectId,
        'prompt_text' => $pe['prompt'],
        'neutrality' => $pe['neutrality'],
        'functional_stability' => $pe['stability'],
        'logical_soundness' => $pe['soundness'],
        'anti_hallucination' => $pe['anti_hall'],
        'language' => $pe['lang'],
    ];

    $insertResult = $supabase->from('prompt_evaluations')->insert($data);

    if ($insertResult['status'] >= 200 && $insertResult['status'] < 300) {
        $insertCount++;
    } else {
        echo "Error inserting: {$pe['prompt']}\n";
        print_r($insertResult);
        $errorCount++;
    }
}

echo "\n=== Summary ===\n";
echo "Inserted: {$insertCount}\n";
echo "Errors: {$errorCount}\n";
echo "Total: " . count($promptEvaluations) . "\n";
