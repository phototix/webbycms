<?php

declare(strict_types=1);

namespace WebbyCMS\Cache;

/**
 * File-backed cache. Simple, reliable and suitable for a single instance.
 */
final class FilesystemCache implements CacheInterface
{
    private readonly string $dir;

    public function __construct(string $dir, private readonly string $prefix = '')
    {
        $this->dir = rtrim($dir, '/\\');

        if (!is_dir($this->dir) && !@mkdir($this->dir, 0775, true) && !is_dir($this->dir)) {
            throw new \RuntimeException("Cache directory is not writable: {$this->dir}");
        }
    }

    public function get(string $key): mixed
    {
        $file = $this->file($key);

        if (!is_file($file)) {
            return null;
        }

        $payload = @unserialize((string) file_get_contents($file), ['allowed_classes' => false]);

        if (!is_array($payload) || !array_key_exists('expires', $payload) || !array_key_exists('data', $payload)) {
            return null;
        }

        if ($payload['expires'] !== 0 && $payload['expires'] <= time()) {
            @unlink($file);

            return null;
        }

        return $payload['data'];
    }

    public function set(string $key, mixed $value, int $ttl = 0): void
    {
        $payload = serialize([
            'expires' => $ttl > 0 ? time() + $ttl : 0,
            'data' => $value,
        ]);

        @file_put_contents($this->file($key), $payload, LOCK_EX);
    }

    public function delete(string $key): void
    {
        $file = $this->file($key);

        if (is_file($file)) {
            @unlink($file);
        }
    }

    public function has(string $key): bool
    {
        return $this->get($key) !== null;
    }

    public function remember(string $key, int $ttl, callable $callback): mixed
    {
        $cached = $this->get($key);

        if ($cached !== null) {
            return $cached;
        }

        $value = $callback();
        $this->set($key, $value, $ttl);

        return $value;
    }

    private function file(string $key): string
    {
        return $this->dir . '/' . $this->prefix . md5($key) . '.cache';
    }
}
