<?php

namespace Unimatrix\Backend\Test\TestCase\Controller;

use Cake\TestSuite\TestCase;
use Cake\Http\ServerRequest;
use Unimatrix\Backend\Controller\AppController;

class AppControllerTest extends TestCase
{
    public function testSomething() {
        $request = new ServerRequest();
        $request = $request->withParam('prefix', 'backend');
        $controller = new AppController($request);
        $this->assertInstanceOf(AppController::class, $controller);
    }
}
