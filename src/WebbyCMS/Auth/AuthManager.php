<?php

declare(strict_types=1);

namespace WebbyCMS\Auth;

use WebbyCMS\Session;

/**
 * Session-backed authentication manager.
 *
 * This is the "auth API only": it provides the primitives (login, logout,
 * check, user, attempt) but no login pages or flows.
 */
final class AuthManager
{
    private const ID_KEY = 'webbycms.auth.id';
    private const CLAIMS_KEY = 'webbycms.auth.claims';

    public function __construct(
        private readonly Session $session,
        private readonly ?UserProviderInterface $provider = null,
    ) {
    }

    public function login(int|string $id, array $claims = []): void
    {
        $this->session->regenerate();
        $this->session->put(self::ID_KEY, $id);
        $this->session->put(self::CLAIMS_KEY, $claims);
    }

    public function logout(): void
    {
        $this->session->regenerate();
        $this->session->forget(self::ID_KEY);
        $this->session->forget(self::CLAIMS_KEY);
    }

    public function check(): bool
    {
        return $this->session->has(self::ID_KEY);
    }

    public function guest(): bool
    {
        return !$this->check();
    }

    public function id(): int|string|null
    {
        $id = $this->session->get(self::ID_KEY);

        return is_int($id) || is_string($id) ? $id : null;
    }

    /**
     * @return array<string, mixed>
     */
    public function claims(): array
    {
        $claims = $this->session->get(self::CLAIMS_KEY, []);

        return is_array($claims) ? $claims : [];
    }

    /**
     * Load the full user record for the current session user.
     */
    public function user(): ?array
    {
        $id = $this->id();

        if ($id === null || $this->provider === null) {
            return null;
        }

        return $this->provider->findById($id);
    }

    /**
     * Attempt to authenticate a user from a credentials array.
     *
     * @param array<string, mixed> $credentials
     */
    public function attempt(array $credentials, ?UserProviderInterface $provider = null): bool
    {
        $resolver = $provider ?? $this->provider;

        if ($resolver === null) {
            return false;
        }

        $user = $resolver->findByCredentials($credentials);

        if ($user === null || !$resolver->verifyPassword($user, (string) ($credentials['password'] ?? ''))) {
            return false;
        }

        $id = $user['id'] ?? null;

        if ($id === null) {
            return false;
        }

        $this->login($id, $this->claimsFromUser($user));

        return true;
    }

    /**
     * @param array<string, mixed> $user
     *
     * @return array<string, mixed>
     */
    private function claimsFromUser(array $user): array
    {
        return [
            'name' => $user['name'] ?? null,
            'roles' => is_array($user['roles'] ?? null) ? $user['roles'] : [],
        ];
    }
}
