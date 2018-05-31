<?php

namespace Unimatrix\Backend\Test\TestCase\Http\Middleware;

use Cake\TestSuite\TestCase;
use Cake\Core\Configure;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\Http\Cookie\CookieCollection;
use Cake\Utility\CookieCryptTrait;
use Unimatrix\Backend\Http\Middleware\EncryptedCookieMiddleware;

class EncryptedCookieMiddlewareTest extends TestCase
{
    use CookieCryptTrait;

    protected $encryption = 'aes';
    protected $middleware;
    protected $request;
    protected $response;

    protected function _getCookieEncryptionKey() {
        return 'super secret key that no one can guess';
    }

    public function setUp() {
        Configure::write('Backend.security.salt', $this->_getCookieEncryptionKey());
        $this->middleware = new EncryptedCookieMiddleware($this->encryption);
        $this->request = new ServerRequest();
        $this->response = new Response();
    }

    public function tearDown() {
        parent::tearDown();
        Configure::delete('Backend');
    }

    public function testDecodeRequestCookies() {
        $this->request = $this->request->withCookieParams([
            'secret' => 'not encrypted',
            'backend_secret' => $this->_encrypt('should be encrypted', $this->encryption)
        ]);

        $next = function ($req, $res) {
            $this->assertSame('not encrypted', $req->getCookie('secret'));
            $this->assertSame('should be encrypted', $req->getCookie('backend_secret'));
            return $res;
        };
        $middleware = $this->middleware;
        $middleware($this->request, $this->response, $next);
    }

    public function testEncodeResponseSetCookieHeader() {
        $next = function ($req, $res) {
            return $res->withAddedHeader('Set-Cookie', 'secret=not encrypted')
                ->withAddedHeader('Set-Cookie', 'backend_secret=should be encrypted');
        };
        $middleware = $this->middleware;
        $response = $middleware($this->request, $this->response, $next);

        $this->assertContains('secret=not%20encrypted', $response->getHeaderLine('Set-Cookie'));
        $this->assertNotContains('backend_secret=should%20be%20encrypted', $response->getHeaderLine('Set-Cookie'));

        $cookies = CookieCollection::createFromHeader($response->getHeader('Set-Cookie'));
        $this->assertEquals('not encrypted', $cookies->get('secret')->getValue());
        $this->assertEquals('should be encrypted', $this->_decrypt($cookies->get('backend_secret')->getValue(), $this->encryption));
    }

    public function testEncodeResponseCookieData() {
        $next = function ($req, $res) {
            return $res->withCookie('plain', 'not encrypted')
                ->withCookie('backend_secret', 'should be encrypted');
        };
        $middleware = $this->middleware;
        $response = $middleware($this->request, $this->response, $next);

        $this->assertSame('not encrypted', $response->getCookie('plain')['value']);
        $this->assertSame('should be encrypted', $this->_decrypt($response->getCookie('backend_secret')['value'], $this->encryption));
    }
}
