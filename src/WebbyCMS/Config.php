<?php

declare(strict_types=1);

namespace WebbyCMS;

/**
 * Typed access to application configuration.
 *
 * Values come from the environment (.env file / real environment variables).
 */
final class Config
{
    /**
     * @param array<string, mixed> $overrides
     */
    public function __construct(private readonly array $overrides = [])
    {
    }

    /**
     * Load configuration from environment variables (and an optional .env file).
     */
    public static function fromEnv(string $dir): self
    {
        if (is_file($dir . '/.env')) {
            $dotenv = \Dotenv\Dotenv::createImmutable($dir);
            try {
                $dotenv->safeLoad();
            } catch (\Throwable) {
                // An invalid .env must never take the site down.
            }
        }

        return new self();
    }

    public function get(string $key, mixed $default = null): mixed
    {
        if (array_key_exists($key, $this->overrides)) {
            return $this->overrides[$key];
        }

        $value = getenv($key);

        if ($value === false) {
            $value = $_ENV[$key] ?? null;
        }

        return $value !== null && $value !== false ? $value : $default;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->overrides)
            || getenv($key) !== false
            || isset($_ENV[$key]);
    }

    public function bool(string $key, bool $default = false): bool
    {
        $value = $this->get($key);

        if ($value === null || $value === '') {
            return $default;
        }

        if (is_bool($value)) {
            return $value;
        }

        return filter_var($value, FILTER_VALIDATE_BOOL);
    }

    public function int(string $key, int $default = 0): int
    {
        $value = $this->get($key);

        if ($value === null || $value === '') {
            return $default;
        }

        return (int) $value;
    }
}
