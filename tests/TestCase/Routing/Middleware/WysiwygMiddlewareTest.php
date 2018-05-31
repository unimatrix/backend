<?php

namespace Unimatrix\Backend\Test\TestCase\Routing\Middleware;

use Cake\TestSuite\TestCase;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Http\ServerRequest;
use Cake\Http\Response;
use Cake\Filesystem\Folder;
use Unimatrix\Backend\Routing\Middleware\WysiwygMiddleware;

class WysiwygMiddlewareTest extends TestCase
{
    protected $path = PLUGIN_PATH . DS . 'test-ckfinder';

    public function tearDown() {
        parent::tearDown();
        $_GET = [];
        Plugin::unload();
        $folder = new Folder($this->path);
        $folder->delete();
    }

    public function testNotCkFinder() {
        $request = new ServerRequest(['url' => '/my-test-path']);
        $response = new Response();
        $next = function ($req, $res) {
            $this->assertEmpty((string)$res);
            $this->assertSame('text/html', $res->getType());
        };
        $middleware = new WysiwygMiddleware();
        $middleware($request, $response, $next);
    }

    public function testPluginNotLoaded() {
        $request = new ServerRequest([
            'url' => '/unimatrix/backend/js/scripts/ckfinder/core/connector/php/connector.php'
        ]);
        $response = new Response();
        $next = function ($req, $res) {
            $this->assertEmpty((string)$res);
            $this->assertSame('text/html', $res->getType());
        };
        $middleware = new WysiwygMiddleware();
        $middleware($request, $response, $next);
    }

    public function testSuccessResponse() {
        Configure::write('Backend.ckfinder', [
            'tmp' => sys_get_temp_dir() . DS,
            'backend' => [
                'name' => 'default',
                'adapter' => 'local',
                'root' => $this->path,
            ]
        ]);
        Plugin::load('Unimatrix/Backend', ['path' => PLUGIN_PATH . DS]);
        $request = new ServerRequest([
            'url' => 'unimatrix/backend/js/scripts/ckfinder/core/connector/php/connector.php'
        ]);
        $_GET = [
            'command' => 'Init',
            'lang' => 'en',
            'type' => 'Images'
        ];
        $response = new Response();
        $next = function ($req, $res) {};
        $middleware = new WysiwygMiddleware();
        $response = $middleware($request, $response, $next);

        $this->assertSame('application/json', $response->getType());
        $this->assertContains('resourceTypes":[{"name":"Images","allowedExtensions', (string)$response);
    }
}
