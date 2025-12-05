<?php

namespace App\Services;

/**
 * Simple file-based cache for reducing Supabase API calls
 */
class Cache
{
    private static string $cacheDir = '/tmp/sgeo_cache';
    private static int $defaultTtl = 300; // 5 minutes

    public static function get(string $key): mixed
    {
        $file = self::getFilePath($key);

        if (!file_exists($file)) {
            return null;
        }

        $data = unserialize(file_get_contents($file));

        if ($data['expires'] < time()) {
            unlink($file);
            return null;
        }

        return $data['value'];
    }

    public static function set(string $key, mixed $value, int $ttl = null): void
    {
        if (!is_dir(self::$cacheDir)) {
            mkdir(self::$cacheDir, 0755, true);
        }

        $data = [
            'value' => $value,
            'expires' => time() + ($ttl ?? self::$defaultTtl),
        ];

        file_put_contents(self::getFilePath($key), serialize($data));
    }

    public static function remember(string $key, callable $callback, int $ttl = null): mixed
    {
        $cached = self::get($key);

        if ($cached !== null) {
            return $cached;
        }

        $value = $callback();
        self::set($key, $value, $ttl);

        return $value;
    }

    public static function forget(string $key): void
    {
        $file = self::getFilePath($key);
        if (file_exists($file)) {
            unlink($file);
        }
    }

    public static function flush(): void
    {
        if (is_dir(self::$cacheDir)) {
            $files = glob(self::$cacheDir . '/*.cache');
            foreach ($files as $file) {
                unlink($file);
            }
        }
    }

    private static function getFilePath(string $key): string
    {
        return self::$cacheDir . '/' . md5($key) . '.cache';
    }
}
