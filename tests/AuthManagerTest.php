<?php

declare(strict_types=1);

namespace WebbyCMS\Tests;

use WebbyCMS\Auth\AuthManager;
use WebbyCMS\Auth\Password;
use WebbyCMS\Auth\UserProviderInterface;
use WebbyCMS\Config;
use WebbyCMS\Session;

final class AuthManagerTest extends TestCase
{
    public function testLoginCheckIdLogout(): void
    {
        $auth = new AuthManager(new Session(new Config(), 'webbycms_session'));

        $this->assertFalse($auth->check());
        $this->assertTrue($auth->guest());

        $auth->login(42, ['roles' => ['admin']]);
        $this->assertTrue($auth->check());
        $this->assertSame(42, $auth->id());
        $this->assertSame(['roles' => ['admin']], $auth->claims());

        $auth->logout();
        $this->assertFalse($auth->check());
        $this->assertNull($auth->id());
    }

    public function testAttemptWithProvider(): void
    {
        $auth = new AuthManager(new Session(new Config(), 'webbycms_session'), $this->provider());

        $this->assertTrue($auth->attempt(['email' => 'brandon@example.com', 'password' => 'secret']));
        $this->assertSame(1, $auth->id());
        $this->assertSame(['name' => 'Brandon', 'roles' => ['admin']], $auth->claims());

        $this->assertFalse($auth->attempt(['email' => 'brandon@example.com', 'password' => 'wrong']));
    }

    public function testUserFromProvider(): void
    {
        $auth = new AuthManager(new Session(new Config(), 'webbycms_session'), $this->provider());

        $this->assertNull($auth->user());
        $auth->login(1);
        $this->assertSame(['id' => 1, 'name' => 'Brandon'], $auth->user());
    }

    public function testAttemptWithoutProviderReturnsFalse(): void
    {
        $auth = new AuthManager(new Session(new Config(), 'webbycms_session'));

        $this->assertFalse($auth->attempt(['email' => 'x', 'password' => 'y']));
    }

    private function provider(): UserProviderInterface
    {
        return new class implements UserProviderInterface {
            public function findById(int|string $id): ?array
            {
                return $id == 1 ? ['id' => 1, 'name' => 'Brandon'] : null;
            }

            public function findByCredentials(array $credentials): ?array
            {
                if (($credentials['email'] ?? null) === 'brandon@example.com') {
                    return ['id' => 1, 'name' => 'Brandon', 'roles' => ['admin'], 'password' => Password::hash('secret')];
                }

                return null;
            }

            public function verifyPassword(array $user, string $password): bool
            {
                return Password::verify($password, $user['password']);
            }
        };
    }
}
