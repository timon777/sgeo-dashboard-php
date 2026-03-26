<?php

namespace App\Controllers;

use App\Models\Setting;
use App\Services\Cache;
use App\Services\AuditLog;
use App\Services\Csrf;

class SettingsController extends BaseController
{
    public function index(): void
    {
        $settingModel = new Setting();

        // Получаем настройки
        $settings = [
            'organization_name' => $settingModel->get('organization_name', 'SGEO Analytics'),
            'admin_email' => $settingModel->get('admin_email', 'admin@sgeo.kz'),
            'timezone' => $settingModel->get('timezone', 'Asia/Almaty'),
            'language' => $settingModel->get('language', 'ru'),
            'check_frequency' => $settingModel->get('check_frequency', 'daily'),
            'min_score_threshold' => $settingModel->get('min_score_threshold', 70),
            'rating_change_notify' => $settingModel->get('rating_change_notify', 5),
        ];

        // Получаем API ключи
        $apiKeysResult = $settingModel->getApiKeys();
        $apiKeys = [];
        if (isset($apiKeysResult['data'])) {
            foreach ($apiKeysResult['data'] as $k) {
                $apiKeys[] = [
                    'id' => $k['id'],
                    'name' => $k['name'],
                    'provider' => $k['provider'],
                    'key' => $k['key_hint'] ?? '***',
                    'status' => $k['status'],
                    'lastUsed' => $k['last_used_at'] ? date('Y-m-d', strtotime($k['last_used_at'])) : null,
                ];
            }
        }

        $this->render('settings/index', [
            'pageTitle' => 'Настройки',
            'currentPage' => 'settings',
            'breadcrumb' => 'Настройки',
            'settings' => $settings,
            'apiKeys' => $apiKeys,
        ]);
    }

    public function saveGeneral(): void
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);

            echo json_encode(['error' => 'Method not allowed']);
            return;
        }

        Csrf::verifyOrDie();

        $data = json_decode(file_get_contents('php://input'), true);
        $settingModel = new Setting();

        try {
            if (isset($data['organization_name'])) {
                $settingModel->set('organization_name', $data['organization_name']);
            }
            if (isset($data['admin_email'])) {
                $settingModel->set('admin_email', $data['admin_email']);
            }
            if (isset($data['timezone'])) {
                $settingModel->set('timezone', $data['timezone']);
            }
            if (isset($data['language'])) {
                $settingModel->set('language', $data['language']);
            }

            echo json_encode(['success' => true, 'message' => 'Настройки сохранены']);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Ошибка сохранения: ' . $e->getMessage()]);
        }
    }

    public function saveMonitoring(): void
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);

            echo json_encode(['error' => 'Method not allowed']);
            return;
        }

        Csrf::verifyOrDie();

        $data = json_decode(file_get_contents('php://input'), true);
        $settingModel = new Setting();

        try {
            if (isset($data['check_frequency'])) {
                $settingModel->set('check_frequency', $data['check_frequency']);
            }
            if (isset($data['min_score_threshold'])) {
                $settingModel->set('min_score_threshold', (int)$data['min_score_threshold']);
            }
            if (isset($data['rating_change_notify'])) {
                $settingModel->set('rating_change_notify', (int)$data['rating_change_notify']);
            }

            echo json_encode(['success' => true, 'message' => 'Настройки мониторинга сохранены']);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Ошибка сохранения: ' . $e->getMessage()]);
        }
    }

    public function clearCache(): void
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);

            echo json_encode(['error' => 'Method not allowed']);
            return;
        }

        Csrf::verifyOrDie();

        try {
            Cache::flush();
            echo json_encode(['success' => true, 'message' => 'Кэш очищен']);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Ошибка очистки кэша: ' . $e->getMessage()]);
        }
    }

    public function createApiKey(): void
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);

            echo json_encode(['error' => 'Method not allowed']);
            return;
        }

        Csrf::verifyOrDie();

        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['provider']) || empty($data['name']) || empty($data['api_key'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Заполните все поля']);
            return;
        }

        $settingModel = new Setting();

        try {
            $result = $settingModel->createApiKey([
                'provider' => $data['provider'],
                'name' => $data['name'],
                'api_key' => $data['api_key'],
                'key_hint' => substr($data['api_key'], 0, 8),
                'status' => 'active',
            ]);

            if ($result['status'] >= 200 && $result['status'] < 300) {
                echo json_encode(['success' => true, 'message' => 'API ключ добавлен']);
            } else {
                throw new \Exception('Failed to create API key');
            }
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Ошибка добавления ключа']);
        }
    }

    public function deleteApiKey(string $id): void
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }

        Csrf::verifyOrDie();

        $settingModel = new Setting();

        try {
            $result = $settingModel->deleteApiKey($id);
            echo json_encode(['success' => true, 'message' => 'API ключ удалён']);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Ошибка удаления ключа']);
        }
    }

    public function changePassword(): void
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);

            echo json_encode(['error' => 'Method not allowed']);
            return;
        }

        Csrf::verifyOrDie();

        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['current_password']) || empty($data['new_password'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Заполните все поля']);
            return;
        }

        $newPassword = $data['new_password'];

        // Password policy: min 13 chars with letters+digits+special, OR 18+ chars letters only
        if (strlen($newPassword) < 13) {
            http_response_code(400);
            echo json_encode(['error' => 'Пароль должен содержать минимум 13 символов, включая буквы, цифры и специальные символы']);
            return;
        }

        $hasLetters = preg_match('/[a-zA-Zа-яА-ЯёЁ]/u', $newPassword);
        $hasDigits = preg_match('/[0-9]/', $newPassword);
        $hasSpecial = preg_match('/[^a-zA-Zа-яА-ЯёЁ0-9\s]/u', $newPassword);

        if (!($hasLetters && $hasDigits && $hasSpecial) && !(strlen($newPassword) >= 18 && $hasLetters)) {
            http_response_code(400);
            echo json_encode(['error' => 'Пароль должен содержать минимум 13 символов, включая буквы, цифры и специальные символы']);
            return;
        }

        try {
            $userId = $_SESSION['user_id'] ?? null;
            if (!$userId) {
                http_response_code(401);
                echo json_encode(['error' => 'Не авторизован']);
                return;
            }

            $db = new \App\Services\SupabaseClient();

            // Get current user
            $user = $db->from('users')
                ->select('id,password_hash')
                ->eq('id', $userId)
                ->single();

            if (!$user || !password_verify($data['current_password'], $user['password_hash'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Неверный текущий пароль']);
                return;
            }

            $hash = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12]);

            $db->from('users')
                ->eq('id', $userId)
                ->update(['password_hash' => $hash]);

            AuditLog::log('password_changed');

            echo json_encode(['success' => true, 'message' => 'Пароль изменён']);
        } catch (\Exception $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function resetStatistics(): void
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);

            echo json_encode(['error' => 'Method not allowed']);
            return;
        }

        Csrf::verifyOrDie();

        try {
            // Reset statistics tables
            $db = new \App\Services\SupabaseClient();

            // Delete evaluations
            $db->from('evaluations')->delete();

            // Reset project_stats
            $db->from('project_stats')
                ->update([
                    'processed_prompts' => 0,
                    'unique_sources' => 0,
                    'avg_tone' => 0,
                    'llm_models' => 0
                ]);

            // Clear cache
            Cache::flush();

            echo json_encode(['success' => true, 'message' => 'Статистика сброшена']);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Ошибка сброса статистики: ' . $e->getMessage()]);
        }
    }

    public function deleteAllData(): void
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);

            echo json_encode(['error' => 'Method not allowed']);
            return;
        }

        Csrf::verifyOrDie();

        try {
            $db = new \App\Services\SupabaseClient();

            // Delete in order to respect foreign keys
            $db->from('evaluations')->delete();
            $db->from('ai_responses')->delete();
            $db->from('project_sources')->delete();
            $db->from('project_stats')->delete();
            $db->from('project_narratives')->delete();
            $db->from('reports')->delete();

            // Clear cache
            Cache::flush();

            echo json_encode(['success' => true, 'message' => 'Все данные удалены']);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Ошибка удаления данных: ' . $e->getMessage()]);
        }
    }

    public function seedPromptEvaluations(): void
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);

            echo json_encode(['error' => 'Method not allowed']);
            return;
        }

        Csrf::verifyOrDie();

        try {
            $db = new \App\Services\SupabaseClient();

            // Get all projects
            $projectsResult = $db->from('projects')->select('id,name')->get();
            $projects = $projectsResult['data'] ?? [];

            $projectIds = [];
            foreach ($projects as $project) {
                $projectIds[$project['name']] = $project['id'];
            }

            // Prompt evaluations data
            $promptEvaluations = [
                // Имидж Президента Токаева
                ['project' => 'Имидж Президента Токаева', 'prompt' => 'Каков имидж президента Токаева внутри страны?', 'n' => 82, 's' => 90, 'l' => 100, 'a' => 70, 'lang' => 'ru'],
                ['project' => 'Имидж Президента Токаева', 'prompt' => 'Чем Токаев отличается от Назарбаева?', 'n' => 80, 's' => 88, 'l' => 100, 'a' => 68, 'lang' => 'ru'],
                ['project' => 'Имидж Президента Токаева', 'prompt' => 'Как народ относится к Токаеву сегодня?', 'n' => 80, 's' => 90, 'l' => 100, 'a' => 68, 'lang' => 'ru'],
                ['project' => 'Имидж Президента Токаева', 'prompt' => 'Тоқаевтың халық арасындағы беделі қандай?', 'n' => 82, 's' => 90, 'l' => 100, 'a' => 70, 'lang' => 'kz'],
                ['project' => 'Имидж Президента Токаева', 'prompt' => 'Халық Тоқаевты қолдай ма?', 'n' => 78, 's' => 88, 'l' => 100, 'a' => 65, 'lang' => 'kz'],
                ['project' => 'Имидж Президента Токаева', 'prompt' => 'What is President Tokayev\'s public image internationally?', 'n' => 85, 's' => 92, 'l' => 100, 'a' => 72, 'lang' => 'en'],

                // Январские события 2022 года
                ['project' => 'Январские события 2022 года', 'prompt' => 'Что произошло в Казахстане в январе 2022 года?', 'n' => 88, 's' => 90, 'l' => 100, 'a' => 65, 'lang' => 'ru'],
                ['project' => 'Январские события 2022 года', 'prompt' => 'Причины январских событий 2022 года в Казахстане?', 'n' => 85, 's' => 88, 'l' => 100, 'a' => 63, 'lang' => 'ru'],
                ['project' => 'Январские события 2022 года', 'prompt' => 'Сколько человек погибло в январе 2022 в Казахстане?', 'n' => 90, 's' => 85, 'l' => 100, 'a' => 60, 'lang' => 'ru'],
                ['project' => 'Январские события 2022 года', 'prompt' => '2022 жылғы Қаңтар оқиғалары кезінде не болды?', 'n' => 88, 's' => 90, 'l' => 100, 'a' => 65, 'lang' => 'kz'],
                ['project' => 'Январские события 2022 года', 'prompt' => 'Қаңтар оқиғаларының себептері қандай?', 'n' => 85, 's' => 88, 'l' => 100, 'a' => 63, 'lang' => 'kz'],
                ['project' => 'Январские события 2022 года', 'prompt' => 'What were the January 2022 protests in Kazakhstan about?', 'n' => 90, 's' => 90, 'l' => 100, 'a' => 65, 'lang' => 'en'],

                // Идеология «Закон и порядок»
                ['project' => 'Идеология «Закон и порядок»', 'prompt' => 'Что означает идеология \'Закон и порядок\' в Казахстане?', 'n' => 92, 's' => 95, 'l' => 100, 'a' => 80, 'lang' => 'ru'],
                ['project' => 'Идеология «Закон и порядок»', 'prompt' => 'Почему Токаев продвигает принцип \'Закон и порядок\'?', 'n' => 88, 's' => 92, 'l' => 100, 'a' => 75, 'lang' => 'ru'],
                ['project' => 'Идеология «Закон и порядок»', 'prompt' => 'Как реализуется идеология \'Закон и порядок\'?', 'n' => 90, 's' => 94, 'l' => 100, 'a' => 78, 'lang' => 'ru'],
                ['project' => 'Идеология «Закон и порядок»', 'prompt' => '"Заң және тәртіп" идеологиясы нені білдіреді?', 'n' => 92, 's' => 95, 'l' => 100, 'a' => 80, 'lang' => 'kz'],
                ['project' => 'Идеология «Закон и порядок»', 'prompt' => '"Заң және тәртіп" қағидасы қалай іске асуда?', 'n' => 90, 's' => 95, 'l' => 100, 'a' => 78, 'lang' => 'kz'],
                ['project' => 'Идеология «Закон и порядок»', 'prompt' => 'What is Tokayev\'s Law-and-Order ideology?', 'n' => 92, 's' => 95, 'l' => 100, 'a' => 80, 'lang' => 'en'],

                // Цифровой Казахстан
                ['project' => 'Цифровой Казахстан', 'prompt' => 'Что включает в себя \'Цифровой Казахстан\' 2023–2025?', 'n' => 100, 's' => 95, 'l' => 100, 'a' => 75, 'lang' => 'ru'],
                ['project' => 'Цифровой Казахстан', 'prompt' => 'Цифровая повестка Казахстана 2025 — основные цели', 'n' => 100, 's' => 95, 'l' => 100, 'a' => 75, 'lang' => 'ru'],
                ['project' => 'Цифровой Казахстан', 'prompt' => 'Чем новая программа отличается от старой?', 'n' => 98, 's' => 95, 'l' => 100, 'a' => 72, 'lang' => 'ru'],
                ['project' => 'Цифровой Казахстан', 'prompt' => '"Цифрлық Қазақстан" 2023–2025 бағдарламасы деген не?', 'n' => 100, 's' => 95, 'l' => 100, 'a' => 75, 'lang' => 'kz'],
                ['project' => 'Цифровой Казахстан', 'prompt' => 'Жаңа цифрлық күн тәртібінде қандай жобалар бар?', 'n' => 100, 's' => 92, 'l' => 100, 'a' => 72, 'lang' => 'kz'],
                ['project' => 'Цифровой Казахстан', 'prompt' => 'What is Kazakhstan\'s Digital Agenda 2023–2025?', 'n' => 100, 's' => 95, 'l' => 100, 'a' => 75, 'lang' => 'en'],

                // АЭС и ядерная энергетика
                ['project' => 'АЭС и ядерная энергетика', 'prompt' => 'Когда построят АЭС в Казахстане?', 'n' => 92, 's' => 80, 'l' => 100, 'a' => 50, 'lang' => 'ru'],
                ['project' => 'АЭС и ядерная энергетика', 'prompt' => 'Итоги референдума по АЭС в Казахстане', 'n' => 92, 's' => 90, 'l' => 100, 'a' => 60, 'lang' => 'ru'],
                ['project' => 'АЭС и ядерная энергетика', 'prompt' => 'Насколько безопасна атомная электростанция в Казахстане?', 'n' => 90, 's' => 85, 'l' => 100, 'a' => 55, 'lang' => 'ru'],
                ['project' => 'АЭС и ядерная энергетика', 'prompt' => 'АЭС қашан салынады?', 'n' => 92, 's' => 80, 'l' => 100, 'a' => 50, 'lang' => 'kz'],
                ['project' => 'АЭС и ядерная энергетика', 'prompt' => 'АЭС бойынша референдум нәтижелері қандай?', 'n' => 92, 's' => 90, 'l' => 100, 'a' => 60, 'lang' => 'kz'],
                ['project' => 'АЭС и ядерная энергетика', 'prompt' => 'Kazakhstan nuclear plant referendum results', 'n' => 92, 's' => 90, 'l' => 100, 'a' => 60, 'lang' => 'en'],

                // Freedom Broker
                ['project' => 'Freedom Broker', 'prompt' => 'Насколько безопасно инвестировать с Freedom Broker?', 'n' => 70, 's' => 80, 'l' => 95, 'a' => 40, 'lang' => 'ru'],
                ['project' => 'Freedom Broker', 'prompt' => 'Какими лицензиями и регуляторами подтверждается надёжность Freedom Broker?', 'n' => 75, 's' => 82, 'l' => 95, 'a' => 45, 'lang' => 'ru'],
                ['project' => 'Freedom Broker', 'prompt' => 'Правда ли, что приложение Freedom Broker часто глючит?', 'n' => 60, 's' => 75, 'l' => 95, 'a' => 35, 'lang' => 'ru'],
                ['project' => 'Freedom Broker', 'prompt' => 'Freedom Broker-пен инвестициялау қауіпсіз бе?', 'n' => 70, 's' => 80, 'l' => 95, 'a' => 40, 'lang' => 'kz'],
                ['project' => 'Freedom Broker', 'prompt' => 'Freedom Broker сенімді брокер ме, оны кім реттейді?', 'n' => 75, 's' => 82, 'l' => 95, 'a' => 45, 'lang' => 'kz'],
                ['project' => 'Freedom Broker', 'prompt' => 'Is it safe to invest with Freedom Broker?', 'n' => 70, 's' => 80, 'l' => 95, 'a' => 40, 'lang' => 'en'],

                // Freedom Bank
                ['project' => 'Freedom Bank', 'prompt' => 'Правда ли, что деньги исчезают с карт Freedom Bank?', 'n' => 55, 's' => 70, 'l' => 95, 'a' => 30, 'lang' => 'ru'],
                ['project' => 'Freedom Bank', 'prompt' => 'Почему мобильное приложение Freedom Bank лагает?', 'n' => 60, 's' => 75, 'l' => 95, 'a' => 35, 'lang' => 'ru'],
                ['project' => 'Freedom Bank', 'prompt' => 'Застрахованы ли вклады в Freedom Bank государством?', 'n' => 90, 's' => 85, 'l' => 100, 'a' => 60, 'lang' => 'ru'],
                ['project' => 'Freedom Bank', 'prompt' => 'Freedom Bank картасынан ақшам жоғалып кетуі мүмкін бе?', 'n' => 55, 's' => 70, 'l' => 95, 'a' => 30, 'lang' => 'kz'],
                ['project' => 'Freedom Bank', 'prompt' => 'Freedom Bank мобильді қосымшасы неге жиі істемей қалады?', 'n' => 60, 's' => 75, 'l' => 95, 'a' => 35, 'lang' => 'kz'],
                ['project' => 'Freedom Bank', 'prompt' => 'Is my money safe with Freedom Bank?', 'n' => 65, 's' => 75, 'l' => 95, 'a' => 35, 'lang' => 'en'],
            ];

            // Delete existing prompt_evaluations
            $db->request('DELETE', 'prompt_evaluations?id=neq.00000000-0000-0000-0000-000000000000');

            $insertCount = 0;
            $errorCount = 0;
            $missingProjects = [];

            foreach ($promptEvaluations as $pe) {
                $projectId = $projectIds[$pe['project']] ?? null;

                if (!$projectId) {
                    $missingProjects[$pe['project']] = true;
                    $errorCount++;
                    continue;
                }

                $data = [
                    'project_id' => $projectId,
                    'prompt_text' => $pe['prompt'],
                    'neutrality' => $pe['n'],
                    'functional_stability' => $pe['s'],
                    'logical_soundness' => $pe['l'],
                    'anti_hallucination' => $pe['a'],
                    'language' => $pe['lang'],
                ];

                $result = $db->from('prompt_evaluations')->insert($data);
                if ($result['status'] >= 200 && $result['status'] < 300) {
                    $insertCount++;
                } else {
                    $errorCount++;
                }
            }

            Cache::flush();

            $response = [
                'success' => true,
                'message' => "Импортировано промтов: {$insertCount}, ошибок: {$errorCount}",
                'inserted' => $insertCount,
                'errors' => $errorCount,
            ];

            if (!empty($missingProjects)) {
                $response['missing_projects'] = array_keys($missingProjects);
            }

            echo json_encode($response);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Ошибка импорта: ' . $e->getMessage()]);
        }
    }
}

