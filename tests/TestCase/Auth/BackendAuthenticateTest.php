<?php

namespace Unimatrix\Backend\Test\TestCase\Auth;

use Cake\TestSuite\TestCase;
use Cake\Core\Configure;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\Controller\Controller;
use Cake\Controller\ComponentRegistry;
use Unimatrix\Backend\Auth\BackendAuthenticate;
use RuntimeException;

class BackendAuthenticateTest extends TestCase
{
    protected $request;
    protected $registry;
    protected $response;

    public function setUp() {
        parent::setUp();
        $controller = new Controller();
        $this->request = new ServerRequest();
        $this->response = new Response();
        $this->registry = new ComponentRegistry($controller);
    }

    public function testNoBackendCredentials() {
        $this->expectException(RuntimeException::class);

        $auth = new BackendAuthenticate($this->registry);
        $auth->authenticate($this->request, $this->response);
    }

    public function testEmptyDataInRequest() {
        Configure::write('Backend.credentials', ['username' => 'user', 'password' => 'pass']);
        $auth = new BackendAuthenticate($this->registry);
        $this->assertFalse($auth->authenticate($this->request, $this->response));
    }

    public function testSuccessFromRequestData() {
        $credentials = ['username' => 'user', 'password' => 'pass'];
        Configure::write('Backend.credentials', $credentials);
        $request = new ServerRequest([
            'post' => $credentials,
            'environment' => [
                'REQUEST_METHOD' => 'GET'
            ]
        ]);
        $auth = new BackendAuthenticate($this->registry);

        $this->assertArraySubset($credentials, $auth->authenticate($request, $this->response));
    }

    public function testSuccessFromCookieData() {
        $remember = 'my_remember';
        $credentials = ['username' => 'user', 'password' => 'pass'];
        Configure::write('Backend.credentials', $credentials);
        Configure::write('Backend.credentials.cookie', $remember);
        $request = new ServerRequest(['cookies' => [$remember => $credentials]]);
        $auth = new BackendAuthenticate($this->registry);

        $this->assertArraySubset($credentials, $auth->authenticate($request, $this->response));
    }
}
