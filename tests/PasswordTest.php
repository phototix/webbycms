<?php

declare(strict_types=1);

namespace WebbyCMS\Tests;

use WebbyCMS\Auth\Password;

final class PasswordTest extends TestCase
{
    public function testHashAndVerify(): void
    {
        $hash = Password::hash('secret123');

        $this->assertNotSame('secret123', $hash);
        $this->assertTrue(Password::verify('secret123', $hash));
        $this->assertFalse(Password::verify('wrong', $hash));
    }

    public function testNeedsRehash(): void
    {
        $hash = Password::hash('secret123');

        $this->assertIsBool(Password::needsRehash($hash));
    }
}
