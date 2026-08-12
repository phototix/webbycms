<?php

declare(strict_types=1);

namespace WebbyCMS;

/**
 * Immutable representation of the incoming HTTP request.
 */
final class Request
{
    /** @var array<string, string> */
    private array $segments = [];

    /**
     * @param array<string, mixed> $server
     * @param array<string, mixed> $query
     * @param array<string, mixed> $post
     * @param array<string, mixed> $cookies
     * @param array<string, mixed> $files
     */
    public function __construct(
        private readonly array $server,
        private readonly array $query,
        private readonly array $post,
        private readonly array $cookies,
        private readonly array $files = [],
        private readonly string $sessionName = 'PHPSESSID',
    ) {
    }

    public static function capture(?string $sessionName = null): self
    {
        return new self(
            $_SERVER,
            $_GET,
            $_POST,
            $_COOKIE,
            $_FILES,
            $sessionName ?? session_name(),
        );
    }

    public function method(): string
    {
        return strtoupper((string) ($this->server['REQUEST_METHOD'] ?? 'GET'));
    }

    public function isMethod(string $method): bool
    {
        return $this->method() === strtoupper($method);
    }

    public function isGet(): bool
    {
        return $this->isMethod('GET');
    }

    public function isPost(): bool
    {
        return $this->isMethod('POST');
    }

    public function isAjax(): bool
    {
        return $this->input('formset') === 'ajax'
            || $this->header('X-Requested-With') === 'XMLHttpRequest';
    }

    /**
     * The request path, e.g. /about/team/5.html
     */
    public function path(): string
    {
        $path = parse_url((string) ($this->server['REQUEST_URI'] ?? '/'), PHP_URL_PATH) ?: '/';

        return rawurldecode($path);
    }

    /**
     * The raw request URI (path + query string).
     */
    public function uri(): string
    {
        return (string) ($this->server['REQUEST_URI'] ?? '/');
    }

    /**
     * @return array<string, mixed>
     */
    public function query(): array
    {
        return $this->query;
    }

    /**
     * Fetch a value from POST first, then from the query string.
     */
    public function input(string $key, mixed $default = null): mixed
    {
        return $this->post[$key] ?? $this->query[$key] ?? $default;
    }

    /**
     * @return array<string, mixed>
     */
    public function all(): array
    {
        return array_merge($this->query, $this->post);
    }

    public function has(string $key): bool
    {
        return isset($this->post[$key]) || isset($this->query[$key]);
    }

    /**
     * @return array<string, mixed>
     */
    public function cookies(): array
    {
        return $this->cookies;
    }

    public function hasSessionCookie(): bool
    {
        return isset($this->cookies[$this->sessionName]);
    }

    /**
     * @return array<string, mixed>
     */
    public function files(): array
    {
        return $this->files;
    }

    public function server(string $key, mixed $default = null): mixed
    {
        return $this->server[$key] ?? $default;
    }

    public function header(string $name, mixed $default = null): mixed
    {
        $key = 'HTTP_' . strtoupper(str_replace('-', '_', $name));

        return $this->server[$key] ?? $default;
    }

    public function isSecure(): bool
    {
        $https = (string) ($this->server['HTTPS'] ?? '');
        $forwarded = (string) ($this->server['HTTP_X_FORWARDED_PROTO'] ?? '');

        return ($https !== '' && $https !== 'off') || $forwarded === 'https';
    }

    /**
     * Set the resolved route segments (page, cate, action, ...).
     *
     * @param array<string, string> $segments
     */
    public function setSegments(array $segments): void
    {
        $this->segments = $segments;
    }

    public function segment(string $key, mixed $default = null): mixed
    {
        return $this->segments[$key] ?? $default;
    }

    /**
     * @return array<string, string>
     */
    public function segments(): array
    {
        return $this->segments;
    }
}
