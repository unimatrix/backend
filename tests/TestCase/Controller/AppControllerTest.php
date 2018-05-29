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

namespace Unimatrix\Backend\Test\TestCase\Controller {
    use Cake\TestSuite\TestCase;
    use Cake\Http\ServerRequest;
    use Unimatrix\Backend\Controller\AppController;

    class AppControllerTest extends TestCase
    {
        public function testInitialize() {
            $request = new ServerRequest();
            $controller = new AppController($request);
            $this->assertInstanceOf(AppController::class, $controller);
        }
    }
}
