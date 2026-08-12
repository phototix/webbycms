<?php

declare(strict_types=1);

namespace WebbyCMS\Tests;

use WebbyCMS\Cache\FilesystemCache;

final class FilesystemCacheTest extends TestCase
{
    private string $dir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dir = sys_get_temp_dir() . '/webbycms-cache-' . bin2hex(random_bytes(4));
    }

    protected function tearDown(): void
    {
        foreach (glob($this->dir . '/*.cache') ?: [] as $file) {
            @unlink($file);
        }
        @rmdir($this->dir);

        parent::tearDown();
    }

    public function testSetGetDelete(): void
    {
        $cache = new FilesystemCache($this->dir);

        $this->assertNull($cache->get('foo'));

        $cache->set('foo', ['a' => 1]);
        $this->assertSame(['a' => 1], $cache->get('foo'));

        $cache->delete('foo');
        $this->assertNull($cache->get('foo'));
    }

    public function testExpiry(): void
    {
        $cache = new FilesystemCache($this->dir);
        $cache->set('temp', 'value', 1);

        $this->assertSame('value', $cache->get('temp'));
        sleep(2);
        $this->assertNull($cache->get('temp'));
    }

    public function testRemember(): void
    {
        $cache = new FilesystemCache($this->dir);
        $calls = 0;

        $cache->remember('key', 60, function () use (&$calls) {
            $calls++;

            return 'computed';
        });
        $cache->remember('key', 60, function () use (&$calls) {
            $calls++;

            return 'computed';
        });

        $this->assertSame(1, $calls);
        $this->assertSame('computed', $cache->get('key'));
    }
}
