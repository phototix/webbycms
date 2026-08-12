<?php

declare(strict_types=1);

namespace WebbyCMS\Tests;

use WebbyCMS\Config;
use WebbyCMS\Session;

final class SessionTest extends TestCase
{
    public function testPutGetForget(): void
    {
        $session = new Session(new Config(), 'webbycms_session');

        $this->assertFalse($session->isStarted());

        $session->put('name', 'Brandon');
        $this->assertTrue($session->isStarted());
        $this->assertSame('Brandon', $session->get('name'));
        $this->assertTrue($session->has('name'));
        $this->assertNull($session->get('missing'));

        $session->forget('name');
        $this->assertFalse($session->has('name'));
    }

    public function testRegenerateKeepsData(): void
    {
        $session = new Session(new Config(), 'webbycms_session');
        $session->put('key', 'value');

        $before = $session->id();
        $session->regenerate();
        $after = $session->id();

        $this->assertNotSame($before, $after);
        $this->assertSame('value', $session->get('key'));
    }

    public function testFlushAndDestroy(): void
    {
        $session = new Session(new Config(), 'webbycms_session');
        $session->put('a', 1);
        $session->put('b', 2);

        $session->flush();
        $this->assertNull($session->get('a'));
        $this->assertNull($session->get('b'));
    }
}
