<?php

namespace Unimatrix\Backend\Test\TestCase;

use Cake\TestSuite\TestCase;
use Cake\Core\Configure;
use Cake\Http\MiddlewareQueue;
use Cake\Routing\Router;
use Cake\Routing\Middleware\AssetMiddleware;
use Unimatrix\Backend\Plugin;
use Unimatrix\Backend\Routing\Middleware\WysiwygMiddleware;
use Unimatrix\Backend\Http\Middleware\CsrfProtectionMiddleware;
use Unimatrix\Backend\Http\Middleware\EncryptedCookieMiddleware;

class PluginTest extends TestCase
{
    protected $server = null;
    protected $plugin;
    protected $stack;

    public function setUp() {
        parent::setUp();
        $this->server = $_SERVER;
        $this->stack = new MiddlewareQueue();
        $this->stack->add(AssetMiddleware::class);
        $this->plugin = new Plugin();
    }

    public function tearDown() {
        parent::tearDown();
        $_SERVER = $this->server;
    }

    public function testPluginName() {
        $this->assertEquals('Unimatrix/Backend', $this->plugin->getName());
    }

    public function testNoConfigDefinedForBackend() {
        Configure::write('Backend', true);
        $middleware = $this->plugin->middleware($this->stack);
        $this->assertCount(1, $middleware);
    }

    public function testUrlNotMatchedAsBackend() {
        $middleware = $this->plugin->middleware($this->stack);
        $this->assertCount(1, $middleware);
    }

    public function testMiddlewareLoadedAsBackend() {
        $_SERVER['REQUEST_URI'] = '/backend';
        Configure::write('Backend.security.enabled', true);
        Configure::write('Backend.security.salt', '0123456789012345678901234567890123456789012345678901234567890123');
        $middleware = $this->plugin->middleware($this->stack);

        $this->assertInstanceOf(MiddlewareQueue::class, $middleware);
        $this->assertInstanceOf(WysiwygMiddleware::class, $middleware->get(0));
        $this->assertInstanceOf(AssetMiddleware::class, $middleware->get(1));
        $this->assertInstanceOf(EncryptedCookieMiddleware::class, $middleware->get(2));
        $this->assertInstanceOf(CsrfProtectionMiddleware::class, $middleware->get(3));
        $this->assertCount(4, $middleware);
    }

    public function testRoutes() {
        $builder = Router::createRouteBuilder('/');
        $routes = $this->plugin->routes($builder);
        $collection = Router::getRouteCollection();
        $expected = [
            '/backend/login',
            '/backend/logout',
            '/backend/:controller',
            '/backend/:controller/:action/*'
        ];
        foreach($collection->routes() as $idx => $one)
            $this->assertEquals($expected[$idx], $one->template);
    }
}
