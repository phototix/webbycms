<?php

declare(strict_types=1);

namespace WebbyCMS\Tests;

use WebbyCMS\Response;

final class ResponseTest extends TestCase
{
    public function testHtml(): void
    {
        $response = Response::html('<h1>Hi</h1>', 200);

        $this->assertSame(200, $response->status());
        $this->assertSame('<h1>Hi</h1>', $response->body());
        $this->assertTrue($response->isCacheable());
    }

    public function testRedirect(): void
    {
        $response = Response::redirect('/home', 303);

        $this->assertSame(303, $response->status());
        $this->assertSame('/home', $response->headers()['Location']);
        $this->assertFalse($response->isCacheable() === false);
    }

    public function testJson(): void
    {
        $response = Response::json(['ok' => true]);

        $this->assertSame(200, $response->status());
        $this->assertSame('application/json; charset=utf-8', $response->headers()['Content-Type']);
        $this->assertSame('{"ok":true}', $response->body());
    }

    public function testErrorIsNotCacheable(): void
    {
        $this->assertFalse(Response::html('nope', 500)->isCacheable());
        $this->assertFalse(Response::html('nope', 404)->isCacheable());
    }
}
