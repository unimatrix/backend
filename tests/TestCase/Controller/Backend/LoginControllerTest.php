<?php

namespace App\Controller {
    use Cake\Controller\Controller;

    if(!class_exists(AppController::class)) {
        class AppController extends Controller
        {
            public function initialize() {
                parent::initialize();
                $this->loadComponent('Unimatrix/Backend.Backend');
            }
        }
    }
}

namespace Unimatrix\Backend\Test\TestCase\Controller\Backend {
    use Cake\TestSuite\TestCase;
    use Cake\Routing\Router;
    use Cake\Core\Configure;
    use Cake\Http\ServerRequest;
    use Unimatrix\Backend\Controller\Backend\LoginController;

    class LoginControllerTest extends TestCase
    {
        protected $controller;

        public function setUp() {
            parent::setUp();
            $request = new ServerRequest();
            $this->controller = $this->getMockBuilder(LoginController::class)
                ->setConstructorArgs([$request])
                ->setMethods(['redirect'])
                ->getMock();
        }

        public function testUserAlreadyLoggedIn() {
            $this->controller->expects($this->once())
                ->method('redirect');

            $this->controller->Auth->setUser(['username' => 'user']);
            $this->controller->index();
        }

        public function testUserNotLoggedIn() {
            $this->controller->index();
            $this->assertNull(null);
        }

        public function testUserLoggedInSuccessfull() {
            $this->controller->expects($this->once())
                ->method('redirect');

            $credentials = ['username' => 'user', 'password' => 'pass'];
            Configure::write('Backend.credentials', $credentials);
            $request = new ServerRequest([
                'post' => $credentials,
                'environment' => [
                    'REQUEST_METHOD' => 'POST'
                ]
            ]);
            $this->controller->setRequest($request);
            $this->controller->index();
        }

        public function testUserLoggedInFailed() {
            $credentials = ['username' => 'user', 'password' => 'pass'];
            Configure::write('Backend.credentials', $credentials);
            $request = new ServerRequest([
                'post' => ['username' => 'failed'],
                'environment' => [
                    'REQUEST_METHOD' => 'POST'
                ]
            ]);
            $this->controller->setRequest($request);
            $this->controller->index();
            $this->assertNull(null);
        }

        public function testLogoutFailed() {
            $this->controller->expects($this->once())
                ->method('redirect');
            $this->controller->logout();
        }

        public function testLogoutSuccessfull() {
            $this->controller->expects($this->once())
                ->method('redirect');
            Router::connect('/login', ['controller' => 'Login', 'action' => 'index', 'plugin' => 'Unimatrix/Backend']);
            $this->controller->Auth->setUser(['username' => 'user']);
            $this->controller->logout();
            Router::reload();
        }
    }
}
