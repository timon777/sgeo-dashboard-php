<?php

namespace App\Controllers;

use App\Models\Setting;

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
}
