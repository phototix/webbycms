<?php

declare(strict_types=1);

namespace WebbyCMS;

/**
 * Hardened, lazily-started PHP session wrapper.
 *
 * The session is only started when data is actually needed, which keeps the
 * anonymous page cache efficient.
 */
final class Session
{
    private bool $started = false;

    public function __construct(
        private readonly Config $config,
        private readonly string $name = 'PHPSESSID',
        private readonly bool $secure = false,
    ) {
    }

    public function name(): string
    {
        return $this->name;
    }

    /**
     * Start (or re-enter) the session exactly once.
     */
    public function start(): void
    {
        if ($this->started || session_status() === PHP_SESSION_ACTIVE) {
            $this->started = true;

            return;
        }

        $lifetime = $this->config->int('SESSION_LIFETIME', 7200);

        session_name($this->name);
        session_set_cookie_params([
            'lifetime' => $lifetime,
            'path' => '/',
            'domain' => '',
            'secure' => $this->secure,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);

        // Strict mode rejects uninitialised session ids (session fixation).
        ini_set('session.use_strict_mode', '1');
        ini_set('session.use_only_cookies', '1');
        ini_set('session.use_trans_sid', '0');

        if (!headers_sent()) {
            session_start();
        } else {
            // Fall back when output has already been emitted: register a read-only handle.
            @session_start();
        }

        $this->started = true;
    }

    public function isStarted(): bool
    {
        return $this->started || session_status() === PHP_SESSION_ACTIVE;
    }

    /**
     * Regenerate the session id while keeping the data (do this on login).
     */
    public function regenerate(): void
    {
        $this->ensureStarted();

        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }
    }

    public function id(): string
    {
        $this->ensureStarted();

        return session_id();
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $this->ensureStarted();

        return $_SESSION[$key] ?? $default;
    }

    public function put(string $key, mixed $value): void
    {
        $this->ensureStarted();
        $_SESSION[$key] = $value;
    }

    public function has(string $key): bool
    {
        return $this->get($key) !== null;
    }

    public function forget(string $key): void
    {
        $this->ensureStarted();
        unset($_SESSION[$key]);
    }

    public function flush(): void
    {
        $this->ensureStarted();
        $_SESSION = [];
    }

    /**
     * End the session and invalidate the session cookie.
     */
    public function destroy(): void
    {
        $this->ensureStarted();

        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                $this->name,
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly'],
            );
        }

        session_destroy();
        $this->started = false;
    }

    private function ensureStarted(): void
    {
        if (!$this->started && session_status() !== PHP_SESSION_ACTIVE) {
            $this->start();
        }
    }
}
