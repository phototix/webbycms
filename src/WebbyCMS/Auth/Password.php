<?php

declare(strict_types=1);

namespace WebbyCMS\Auth;

/**
 * Password hashing helpers built on PHP's native password API.
 */
final class Password
{
    public static function hash(string $password, array $options = []): string
    {
        return password_hash($password, PASSWORD_ARGON2ID, $options);
    }

    public static function verify(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    public static function needsRehash(string $hash): bool
    {
        return password_needs_rehash($hash, PASSWORD_ARGON2ID);
    }
}
