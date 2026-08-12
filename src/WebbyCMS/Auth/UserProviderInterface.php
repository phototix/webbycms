<?php

declare(strict_types=1);

namespace WebbyCMS\Auth;

/**
 * Contract for loading a user record and verifying credentials.
 *
 * Implement this against your own data source (database, file, API, ...).
 * The returned user is an array; the key given to AuthManager::login() is
 * used as the persistent identity stored in the session.
 */
interface UserProviderInterface
{
    public function findById(int|string $id): ?array;

    /**
     * @param array<string, mixed> $credentials
     */
    public function findByCredentials(array $credentials): ?array;

    /**
     * @param array<string, mixed> $user
     */
    public function verifyPassword(array $user, string $password): bool;
}
