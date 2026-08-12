<?php

declare(strict_types=1);

namespace WebbyCMS\Tests;

use WebbyCMS\Auth\AuthManager;
use WebbyCMS\Auth\Gate;
use WebbyCMS\Config;
use WebbyCMS\Session;

final class GateTest extends TestCase
{
    public function testRoleChecks(): void
    {
        $auth = new AuthManager(new Session(new Config(), 'webbycms_session'));
        $gate = new Gate($auth);

        $this->assertFalse($gate->allows('admin'));

        $auth->login(1, ['roles' => ['admin', 'editor']]);

        $this->assertTrue($gate->allows('admin'));
        $this->assertTrue($gate->allows('editor'));
        $this->assertTrue($gate->denies('user'));
        $this->assertTrue($gate->allowsAny(['user', 'admin']));
        $this->assertFalse($gate->allowsAny(['user']));
    }
}
