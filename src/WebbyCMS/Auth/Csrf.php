<?php

declare(strict_types=1);

namespace WebbyCMS\Auth;

use WebbyCMS\Request;
use WebbyCMS\Session;

/**
 * Per-session CSRF token generation and validation.
 *
 * Every form should include the token: <input type="hidden" name="_token" value="<?= csrf_field() ?>">
 */
final class Csrf
{
    private const KEY = 'webbycms.csrf.token';

    public function __construct(private readonly Session $session)
    {
    }

    public function token(): string
    {
        if (!$this->session->has(self::KEY)) {
            $this->session->put(self::KEY, bin2hex(random_bytes(32)));
        }

        return (string) $this->session->get(self::KEY);
    }

    /**
     * The hidden input HTML for use inside a form.
     */
    public function field(): string
    {
        return '<input type="hidden" name="_token" value="' . htmlspecialchars($this->token(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '">';
    }

    public function validate(?string $token): bool
    {
        $expected = $this->session->get(self::KEY);

        if (!is_string($expected) || $token === null || $token === '') {
            return false;
        }

        return hash_equals($expected, $token);
    }

    public function validateRequest(Request $request): bool
    {
        $token = $request->input('_token');

        return $this->validate(is_string($token) ? $token : null);
    }
}
