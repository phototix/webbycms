<?php

declare(strict_types=1);

namespace WebbyCMS\Cache;

interface CacheInterface
{
    public function get(string $key): mixed;

    public function set(string $key, mixed $value, int $ttl = 0): void;

    public function delete(string $key): void;

    public function has(string $key): bool;

    /**
     * Return the cached value or store the result of $callback.
     */
    public function remember(string $key, int $ttl, callable $callback): mixed;
}
