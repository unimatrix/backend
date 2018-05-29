<?php

namespace Unimatrix\Backend\Test\TestCase\Controller\Component;

use Cake\TestSuite\TestCase;
use Cake\Core\Configure;
use Cake\Controller\Controller;
use Cake\Http\ServerRequest;
use Cake\Routing\Router;

class AuthComponentTest extends TestCase
{
    public function testWithoutAutoLoginCookie() {
        $request = new ServerRequest();
        $controller = new Controller($request);
        $controller->loadComponent('Unimatrix/Backend.Backend');

        $this->assertEquals($controller->Auth->user(), $controller->viewVars['auth']);
    }

    public function testAlreadyLoggedIn() {
        $storage = 'My.Auth';
        $credentials = ['username' => 'user'];
        Configure::write('Backend.credentials', $credentials);
        $request = new ServerRequest();
        $session = $request->getSession();
        $session->write($storage, $credentials);
        $controller = new Controller($request);
        $controller->loadComponent('Unimatrix/Backend.Backend', [
            'Auth' => [
                'storage' => [
                    'className' => 'Session',
                    'key' => $storage
                ]
            ]
        ]);

        $this->assertArraySubset($credentials, $controller->viewVars['auth']);
    }

    public function testWithInvalidAutoLoginCookie() {
        $remember = 'my_remember';
        $credentials = ['username' => 'user', 'password' => 'pass'];
        $badCredentials = ['username' => 'user2', 'password' => 'pass2'];
        Configure::write('Backend.credentials', $credentials);
        Configure::write('Backend.credentials.cookie', $remember);
        $request = new ServerRequest(['cookies' => [$remember => $badCredentials]]);
        $controller = new Controller($request);
        $controller->loadComponent('Unimatrix/Backend.Backend');

        $this->assertArrayHasKey($remember, $controller->getResponse()->getCookies());
        $this->assertEmpty($controller->getResponse()->getCookies()[$remember]['value']);
    }

    public function testWithValidAutoLoginCookie() {
        $remember = 'my_remember';
        $credentials = ['username' => 'user', 'password' => 'pass'];
        Configure::write('Backend.credentials', $credentials);
        Configure::write('Backend.credentials.cookie', $remember);
        $request = new ServerRequest(['cookies' => [$remember => $credentials]]);
        $controller = $this->getMockBuilder(Controller::class)
            ->setConstructorArgs([$request])
            ->setMethods(['redirect'])
            ->getMock();
        $controller->expects($this->once())
            ->method('redirect');
        $controller->loadComponent('Unimatrix/Backend.Backend');

        unset($credentials['password']);
        $this->assertArraySubset($credentials, $controller->Auth->user());
    }

    public function testInvalidLogin() {
        $credentials = ['username' => 'user', 'password' => 'pass'];
        Configure::write('Backend.credentials', $credentials);
        $request = new ServerRequest();
        $controller = new Controller($request);
        $controller->loadComponent('Unimatrix/Backend.Backend');

        $this->assertFalse($controller->Auth->login());
    }

    public function testValidLoginAndSetsAutoLoginCookie() {
        $prefix = 'backend';
        $remember = 'my_remember';
        $credentials = ['username' => 'user', 'password' => 'pass'];
        Configure::write('Backend.credentials', $credentials);
        Configure::write('Backend.credentials.cookie', $remember);
        $request = new ServerRequest([
            'post' => ['remember' => 1] + $credentials,
            'environment' => [
                'REQUEST_METHOD' => 'GET'
            ]
        ]);
        $request = $request->withParam('prefix', $prefix);
        $controller = new Controller($request);
        $controller->loadComponent('Unimatrix/Backend.Backend');

        $return = $controller->Auth->login();
        $this->assertEquals('/' . $prefix, $return);
        $this->assertArraySubset($credentials, $controller->Cookie->read($remember));
        unset($credentials['password']);
        $this->assertArraySubset($credentials, $controller->Auth->user());
    }

    public function testPasswordIsStripped() {
        $credentials = ['username' => 'user', 'password' => 'pass'];
        Configure::write('Backend.credentials', $credentials);
        $request = new ServerRequest();
        $controller = new Controller($request);
        $controller->loadComponent('Unimatrix/Backend.Backend');
        $controller->Auth->setUser($credentials);

        $this->assertArrayNotHasKey('password', $controller->Auth->user());
    }

    public function testLogout() {
        $url = '/logged-out';
        $storage = 'My.Auth';
        $credentials = ['username' => 'user', 'password' => 'pass'];
        Router::connect($url, ['controller' => 'Willy', 'action' => 'wonka']);
        Configure::write('Backend.credentials', $credentials);
        $request = new ServerRequest();
        $session = $request->getSession();
        $session->write($storage, $credentials);
        $controller = new Controller($request);
        $controller->loadComponent('Unimatrix/Backend.Backend', [
            'Auth' => [
                'loginAction' => [
                    'controller' => 'Willy',
                    'action' => 'wonka',
                    'plugin' => null
                ],
                'storage' => [
                    'className' => 'Session',
                    'key' => $storage
                ]
            ]
        ]);

        $this->assertEquals($url, $controller->Auth->logout());
        $this->assertNull($controller->Auth->user());
    }
}
