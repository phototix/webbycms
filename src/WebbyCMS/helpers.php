<?php

declare(strict_types=1);

/**
 * Global helper functions for the WebbyCMS kernel.
 *
 * These give page templates a short, convenient way to reach the services
 * created during Bootstrap::boot(). They are defined in the global namespace
 * because the page templates under /pages and /includes are not namespaced.
 */

use WebbyCMS\Auth\AuthManager;
use WebbyCMS\Auth\Csrf;
use WebbyCMS\Auth\Gate;
use WebbyCMS\Bootstrap;
use WebbyCMS\Cache\CacheInterface;
use WebbyCMS\Db\Database;
use WebbyCMS\Flash;
use WebbyCMS\Logger;
use WebbyCMS\Request;
use WebbyCMS\Response;
use WebbyCMS\Session;

if (!function_exists('app')) {
    function app(): Bootstrap
    {
        return Bootstrap::instance();
    }
}

if (!function_exists('config')) {
    function config(string $key, mixed $default = null): mixed
    {
        return app()->config()->get($key, $default);
    }
}

if (!function_exists('request')) {
    function request(): Request
    {
        return app()->request();
    }
}

if (!function_exists('session')) {
    function session(): Session
    {
        return app()->session();
    }
}

if (!function_exists('flash')) {
    function flash(): Flash
    {
        return app()->flash();
    }
}

if (!function_exists('csrf')) {
    function csrf(): Csrf
    {
        return app()->csrf();
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field(): string
    {
        return csrf()->field();
    }
}

if (!function_exists('auth')) {
    function auth(): AuthManager
    {
        return app()->auth();
    }
}

if (!function_exists('gate')) {
    function gate(): Gate
    {
        return app()->gate();
    }
}

if (!function_exists('db')) {
    function db(): ?Database
    {
        return app()->db();
    }
}

if (!function_exists('cache')) {
    function cache(): CacheInterface
    {
        return app()->cache();
    }
}

if (!function_exists('logger')) {
    function logger(): Logger
    {
        return app()->logger();
    }
}

if (!function_exists('view')) {
    function view(string $template, array $vars = []): string
    {
        return app()->view()->render($template, $vars);
    }
}

if (!function_exists('e')) {
    function e(mixed $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

if (!function_exists('url')) {
    function url(string $path = ''): string
    {
        $base = rtrim((string) app()->config()->get('APP_URL', ''), '/');

        return $base . '/' . ltrim($path, '/');
    }
}

if (!function_exists('redirect')) {
    function redirect(string $url, int $status = 302): Response
    {
        return Response::redirect($url, $status);
    }
}
