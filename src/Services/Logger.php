<?php

namespace App\Services;

class Logger
{
    private static ?Logger $instance = null;
    private string $logDir;
    private string $logFile;

    private function __construct()
    {
        $this->logDir = __DIR__ . '/../../logs';
        $this->logFile = $this->logDir . '/app.log';

        if (!is_dir($this->logDir)) {
            mkdir($this->logDir, 0755, true);
        }
    }

    public static function getInstance(): Logger
    {
        if (self::$instance === null) {
            self::$instance = new Logger();
        }
        return self::$instance;
    }

    private function write(string $level, string $message, array $context = []): void
    {
        $timestamp = date('Y-m-d H:i:s');
        $contextStr = !empty($context) ? ' ' . json_encode($context, JSON_UNESCAPED_UNICODE) : '';
        $logLine = "[{$timestamp}] [{$level}] {$message}{$contextStr}" . PHP_EOL;

        file_put_contents($this->logFile, $logLine, FILE_APPEND | LOCK_EX);
    }

    public function debug(string $message, array $context = []): void
    {
        $this->write('DEBUG', $message, $context);
    }

    public function info(string $message, array $context = []): void
    {
        $this->write('INFO', $message, $context);
    }

    public function warning(string $message, array $context = []): void
    {
        $this->write('WARNING', $message, $context);
    }

    public function error(string $message, array $context = []): void
    {
        $this->write('ERROR', $message, $context);
    }

    public function critical(string $message, array $context = []): void
    {
        $this->write('CRITICAL', $message, $context);
    }

    /**
     * Log Supabase API request
     */
    public function logApiRequest(string $method, string $endpoint, int $statusCode, ?string $error = null): void
    {
        $context = [
            'method' => $method,
            'endpoint' => $endpoint,
            'status' => $statusCode,
        ];

        if ($error) {
            $context['error'] = $error;
            $this->error("Supabase API Error", $context);
        } else {
            $this->debug("Supabase API Request", $context);
        }
    }

    /**
     * Get recent log entries
     */
    public function getRecentLogs(int $lines = 100): array
    {
        if (!file_exists($this->logFile)) {
            return [];
        }

        $file = file($this->logFile);
        return array_slice($file, -$lines);
    }

    /**
     * Clear log file
     */
    public function clear(): void
    {
        if (file_exists($this->logFile)) {
            file_put_contents($this->logFile, '');
        }
    }
}
