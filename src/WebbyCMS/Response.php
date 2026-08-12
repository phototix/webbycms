<?php

declare(strict_types=1);

namespace WebbyCMS;

/**
 * HTTP response value object.
 */
final class Response
{
    /**
     * @param array<string, string> $headers
     */
    public function __construct(
        private readonly int $status = 200,
        private readonly string $body = '',
        private readonly array $headers = [],
    ) {
    }

    public static function html(string $body, int $status = 200, array $headers = []): self
    {
        return new self($status, $body, $headers);
    }

    public static function redirect(string $url, int $status = 302, array $headers = []): self
    {
        return new self($status, '', ['Location' => $url] + $headers);
    }

    public static function json(mixed $data, int $status = 200, array $headers = []): self
    {
        $headers = ['Content-Type' => 'application/json; charset=utf-8'] + $headers;

        return new self($status, (string) json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), $headers);
    }

    public function status(): int
    {
        return $this->status;
    }

    public function body(): string
    {
        return $this->body;
    }

    /**
     * @return array<string, string>
     */
    public function headers(): array
    {
        return $this->headers;
    }

    /**
     * Responses below 400 can be stored in the anonymous page cache.
     */
    public function isCacheable(): bool
    {
        return $this->status >= 200 && $this->status < 400;
    }

    public function send(): void
    {
        http_response_code($this->status);

        foreach ($this->headers as $name => $value) {
            header($name . ': ' . $value);
        }

        echo $this->body;
    }
}
