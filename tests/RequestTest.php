<?php

declare(strict_types=1);

namespace WebbyCMS\Tests;

use WebbyCMS\Request;

final class RequestTest extends TestCase
{
    public function testMethodAndPath(): void
    {
        $request = $this->makeRequest('/about/team/5.html?page=about');

        $this->assertSame('GET', $request->method());
        $this->assertTrue($request->isGet());
        $this->assertSame('/about/team/5.html', $request->path());
        $this->assertSame('/about/team/5.html?page=about', $request->uri());
    }

    public function testInputMergesPostThenQuery(): void
    {
        $request = $this->makeRequest('/?name=query', 'POST', ['name' => 'post', 'form' => 'sample']);

        $this->assertSame('post', $request->input('name'));
        $this->assertSame('sample', $request->input('form'));
        $this->assertNull($request->input('missing'));
        $this->assertTrue($request->isPost());
    }

    public function testHasSessionCookie(): void
    {
        $request = $this->makeRequest('/', 'GET', [], ['webbycms_session' => 'abc123']);

        $this->assertTrue($request->hasSessionCookie());
    }

    public function testSegments(): void
    {
        $request = $this->makeRequest('/shop/books');
        $request->setSegments(['page' => 'shop', 'cate' => 'books']);

        $this->assertSame('shop', $request->segment('page'));
        $this->assertSame('books', $request->segment('cate'));
        $this->assertNull($request->segment('missing'));
    }

    public function testSecureDetection(): void
    {
        $plain = $this->makeRequest('/', 'GET', [], [], ['HTTPS' => 'off']);
        $secure = $this->makeRequest('/', 'GET', [], [], ['HTTPS' => 'on']);

        $this->assertFalse($plain->isSecure());
        $this->assertTrue($secure->isSecure());
    }
}
