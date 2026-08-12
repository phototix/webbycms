<?php

declare(strict_types=1);

namespace WebbyCMS\Tests;

use WebbyCMS\Bootstrap;
use WebbyCMS\Router;

final class RouterTest extends TestCase
{
    public function testResolvePathSegments(): void
    {
        $router = new Router(realpath(self::ROOT) ?: self::ROOT);

        $this->assertSame(
            ['page' => 'about', 'cate' => 'team', 'action' => 'view', 'id' => '5', 'sub' => '', 'subsub' => ''],
            $router->resolve($this->makeRequest('/about/team/view/5')),
        );
    }

    public function testResolveHtmlSuffix(): void
    {
        $router = new Router(realpath(self::ROOT) ?: self::ROOT);

        $this->assertSame(
            ['page' => 'about', 'cate' => 'team', 'action' => '', 'id' => '', 'sub' => '', 'subsub' => ''],
            $router->resolve($this->makeRequest('/about/team.html')),
        );
    }

    public function testResolveRootToHome(): void
    {
        $router = new Router(realpath(self::ROOT) ?: self::ROOT);

        $this->assertSame('home', $router->resolve($this->makeRequest('/'))['page']);
        $this->assertSame('home', $router->resolve($this->makeRequest('/index.php'))['page']);
    }

    public function testDispatchHomePage(): void
    {
        $app = $this->boot();
        $response = $app->handle($this->makeRequest('/'));

        $this->assertSame(200, $response->status());
        $this->assertStringContainsString('Welcome to Starway Travel', $response->body());
    }

    public function testDispatchHtmlVariant(): void
    {
        $app = $this->boot();
        $response = $app->handle($this->makeRequest('/home.html'));

        $this->assertSame(200, $response->status());
    }

    public function testDispatchMissingPageReturns404(): void
    {
        $app = $this->boot();
        $response = $app->handle($this->makeRequest('/does-not-exist'));

        $this->assertSame(404, $response->status());
        $this->assertStringContainsString('404', $response->body());
    }

    public function testErrorPageStatus(): void
    {
        $app = $this->boot();
        $response = $app->handle($this->makeRequest('/error/500'));

        $this->assertSame(500, $response->status());
        $this->assertStringContainsString('500', $response->body());
    }

    public function testFormPostFlashesAndRedirects(): void
    {
        $app = $this->boot();
        $response = $app->handle($this->makeRequest('/', 'POST', ['form' => 'sample', 'send_something' => 'Hello']));

        $this->assertSame(302, $response->status());
        $this->assertSame('/', $response->headers()['Location']);
        $this->assertTrue($app->flash()->has('success'));
        $this->assertSame('', $app->flash()->peek('error'));
    }

    public function testCsrfEnforcementRejectsMissingToken(): void
    {
        putenv('CSRF_ENFORCE=true');
        Bootstrap::reset();
        $app = $this->boot();

        $response = $app->handle($this->makeRequest('/', 'POST', ['form' => 'sample']));

        $this->assertSame(303, $response->status());
        $this->assertSame('/', $response->headers()['Location']);
        $this->assertTrue($app->flash()->has('error'));
    }
}
