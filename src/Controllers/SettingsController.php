<?php

namespace App\Controllers;

use App\Models\Setting;
use App\Services\Cache;

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

        $settingModel = new Setting();

        try {
            $result = $settingModel->deleteApiKey($id);
            echo json_encode(['success' => true, 'message' => 'API ключ удалён']);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Ошибка удаления ключа']);
        }
    }
}
