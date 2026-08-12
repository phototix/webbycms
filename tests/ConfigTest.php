<?php

declare(strict_types=1);

namespace WebbyCMS\Tests;

use WebbyCMS\Config;

final class ConfigTest extends TestCase
{
    public function testGetReturnsOverride(): void
    {
        $config = new Config(['foo' => 'bar']);

        $this->assertSame('bar', $config->get('foo'));
        $this->assertSame('default', $config->get('missing', 'default'));
    }

    public function testBoolParsesStrings(): void
    {
        $config = new Config(['a' => 'true', 'b' => 'yes', 'c' => '0', 'd' => false]);

        $this->assertTrue($config->bool('a'));
        $this->assertTrue($config->bool('b'));
        $this->assertFalse($config->bool('c'));
        $this->assertFalse($config->bool('d'));
        $this->assertFalse($config->bool('missing'));
        $this->assertTrue($config->bool('missing', true));
    }

    public function testIntParses(): void
    {
        $config = new Config(['a' => '42']);

        $this->assertSame(42, $config->int('a'));
        $this->assertSame(7, $config->int('missing', 7));
    }
}
