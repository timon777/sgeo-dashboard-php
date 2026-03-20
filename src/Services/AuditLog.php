<?php

namespace App\Services;

class AuditLog
{
    public static function log(string $action, string $userId = null, array $details = []): void
    {
        try {
            $db = new SupabaseClient();
            $db->from('audit_logs')->insert([
                'user_id' => $userId ?? ($_SESSION['user_id'] ?? null),
                'action' => $action,
                'details' => json_encode($details, JSON_UNESCAPED_UNICODE),
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
                'created_at' => date('c'),
            ]);
        } catch (\Exception $e) {
            error_log('AuditLog error: ' . $e->getMessage());
        }
    }
}
