<?php

namespace Unimatrix\Backend\Test\TestCase\Http\Middleware;

use Cake\TestSuite\TestCase;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Unimatrix\Backend\Http\Middleware\CsrfProtectionMiddleware;

class CsrfProtectionMiddlewareTest extends TestCase
{
    protected $request;
    protected $response;

    public function setUp() {
        parent::setUp();
        $_SERVER['HTTPS'] = true;
        $this->request = new ServerRequest([
            'environment' => [
                'REQUEST_METHOD' => 'GET'
            ]
        ]);
        $this->request = $this->request->withParam('prefix', 'backend');
        $this->response = new Response();
    }

    public function testCookieSetCorrectly() {
        $next = function ($req, $res) {
            $cookies = $res->getCookies();
            $this->assertArrayHasKey('backend_csrf_token', $cookies);
            $this->assertSame('backend_csrf_token', $cookies['backend_csrf_token']['name']);
            $this->assertSame('backend', $cookies['backend_csrf_token']['path']);
            $this->assertSame(true, $cookies['backend_csrf_token']['secure']);
            $this->assertSame(true, $cookies['backend_csrf_token']['httpOnly']);
        };
        $middleware = new CsrfProtectionMiddleware();
        $middleware($this->request, $this->response, $next);
    }
}
