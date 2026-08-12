<?php

declare(strict_types=1);

namespace WebbyCMS\Auth;

/**
 * Lightweight role/permission checks against the current user's session claims.
 *
 * Claims are set at login time, e.g. login($id, ['roles' => ['admin', 'editor']]).
 */
final class Gate
{
    public function __construct(private readonly AuthManager $auth)
    {
    }

    public function allows(string $role): bool
    {
        return in_array($role, $this->auth->claims()['roles'] ?? [], true);
    }

    public function denies(string $role): bool
    {
        return !$this->allows($role);
    }

    /**
     * Requires a role or returns false when unauthenticated.
     */
    public function allowsAny(array $roles): bool
    {
        foreach ($roles as $role) {
            if ($this->allows($role)) {
                return true;
            }
        }

        return false;
    }
}
