<?php

declare(strict_types=1);

namespace WebbyCMS\Tests;

use WebbyCMS\Auth\Csrf;
use WebbyCMS\Config;
use WebbyCMS\Session;

final class CsrfTest extends TestCase
{
    public function testTokenIsStableAndValidates(): void
    {
        $csrf = new Csrf(new Session(new Config(), 'webbycms_session'));

        $token = $csrf->token();
        $this->assertSame(64, strlen($token));

        // same session -> same token
        $this->assertSame($token, $csrf->token());
        $this->assertTrue($csrf->validate($token));
        $this->assertFalse($csrf->validate('forged'));
        $this->assertFalse($csrf->validate(null));
    }

    public function testFieldContainsToken(): void
    {
        $csrf = new Csrf(new Session(new Config(), 'webbycms_session'));

        $field = $csrf->field();
        $this->assertStringContainsString('name="_token"', $field);
        $this->assertStringContainsString($csrf->token(), $field);
    }
}
