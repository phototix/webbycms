<?php

declare(strict_types=1);

namespace WebbyCMS\Tests;

use WebbyCMS\Config;
use WebbyCMS\Flash;
use WebbyCMS\Session;

final class FlashTest extends TestCase
{
    private Flash $flash;

    protected function setUp(): void
    {
        parent::setUp();
        $this->flash = new Flash(new Session(new Config(), 'webbycms_session'));
    }

    public function testSetGetPeekHas(): void
    {
        $this->assertFalse($this->flash->has('success'));

        $this->flash->success('Saved!');
        $this->assertTrue($this->flash->has('success'));
        $this->assertSame('Saved!', $this->flash->peek('success'));

        // peek does not clear
        $this->assertSame('Saved!', $this->flash->peek('success'));

        // get clears
        $this->assertSame('Saved!', $this->flash->get('success'));
        $this->assertFalse($this->flash->has('success'));
    }

    public function testError(): void
    {
        $this->flash->error('Oops');
        $this->assertSame('Oops', $this->flash->get('error'));
    }
}
