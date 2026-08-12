<?php

declare(strict_types=1);

namespace WebbyCMS;

/**
 * One-shot flash messages rendered on the next page load.
 */
final class Flash
{
    private const PREFIX = 'webbycms.flash.';

    public function __construct(private readonly Session $session)
    {
    }

    public function set(string $type, string $message): void
    {
        $this->session->put(self::PREFIX . $type, $message);
    }

    public function error(string $message): void
    {
        $this->set('error', $message);
    }

    public function success(string $message): void
    {
        $this->set('success', $message);
    }

    /**
     * Read and clear a message.
     */
    public function get(string $type): string
    {
        $message = $this->peek($type);
        $this->session->forget(self::PREFIX . $type);

        return $message;
    }

    /**
     * Read a message without clearing it.
     */
    public function peek(string $type): string
    {
        $message = $this->session->get(self::PREFIX . $type, '');

        return is_string($message) ? $message : '';
    }

    public function has(string $type): bool
    {
        return $this->peek($type) !== '';
    }
}
