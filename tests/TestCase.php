<?php

declare(strict_types=1);

namespace WebbyCMS\Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;
use WebbyCMS\Bootstrap;
use WebbyCMS\Request;

abstract class TestCase extends BaseTestCase
{
    protected const ROOT = __DIR__ . '/..';

    protected function setUp(): void
    {
        parent::setUp();

        // Isolate the singleton kernel and the PHP session between tests.
        Bootstrap::reset();

        if (session_status() === PHP_SESSION_ACTIVE) {
            @session_destroy();
            @session_write_close();
        }

        session_save_path(sys_get_temp_dir());

        // Deterministic, test-friendly environment (wins over .env).
        putenv('APP_DEBUG=true');
        putenv('PAGE_CACHE_ENABLED=false');
        putenv('PAGE_CACHE_TTL=60');
        putenv('CSRF_ENFORCE=false');
    }

    /**
     * Build a request from a URI.
     *
     * @param array<string, mixed> $post
     * @param array<string, mixed> $cookies
     * @param array<string, mixed> $server
     */
    protected function makeRequest(
        string $uri,
        string $method = 'GET',
        array $post = [],
        array $cookies = [],
        array $server = [],
    ): Request {
        $server = array_merge([
            'REQUEST_METHOD' => $method,
            'REQUEST_URI' => $uri,
            'HTTP_HOST' => 'localhost',
            'SERVER_NAME' => 'localhost',
        ], $server);

        return new Request($server, [], $post, $cookies, [], 'webbycms_session');
    }

    protected function boot(): Bootstrap
    {
        return Bootstrap::boot(realpath(self::ROOT) ?: self::ROOT);
    }
}
