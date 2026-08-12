<?php

declare(strict_types=1);

namespace WebbyCMS\Tests;

use WebbyCMS\Bootstrap;
use WebbyCMS\Cache\FilesystemCache;

final class BootstrapTest extends TestCase
{
    public function testBootIsSingleton(): void
    {
        $app = $this->boot();

        $this->assertSame($app, Bootstrap::instance());
        $this->assertTrue(Bootstrap::booted());
    }

    public function testInstanceThrowsBeforeBoot(): void
    {
        Bootstrap::reset();

        $this->expectException(\RuntimeException::class);
        Bootstrap::instance();
    }

    public function testAnonymousStaticPageIsCached(): void
    {
        @session_destroy();

        putenv('PAGE_CACHE_ENABLED=true');
        Bootstrap::reset();
        $app = $this->boot();

        $cacheDir = realpath(self::ROOT) . '/storage/cache';
        foreach (glob($cacheDir . '/*.cache') ?: [] as $file) {
            @unlink($file);
        }

        $first = $app->handle($this->makeRequest('/plain'));
        $this->assertSame(200, $first->status());
        $this->assertStringContainsString('Plain static page', $first->body());

        $cache = new FilesystemCache($cacheDir);
        $this->assertNotNull($cache->get('page:' . md5('/plain')));

        // Second request must be served straight from the cache.
        $second = $app->handle($this->makeRequest('/plain'));
        $this->assertSame($first->body(), $second->body());
    }

    public function testSessionedVisitorIsNotCached(): void
    {
        putenv('PAGE_CACHE_ENABLED=true');
        Bootstrap::reset();
        $app = $this->boot();

        $request = $this->makeRequest('/plain', 'GET', [], ['webbycms_session' => 'deadbeef']);
        $response = $app->handle($request);

        $this->assertSame(200, $response->status());
    }

    public function testSessionNameFromConfig(): void
    {
        $app = $this->boot();

        $this->assertSame('webbycms_session', $app->session()->name());
    }
}
