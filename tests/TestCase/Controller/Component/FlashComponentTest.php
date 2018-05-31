<?php

namespace Unimatrix\Backend\Test\TestCase\Controller\Component;

use Cake\TestSuite\TestCase;
use Cake\Controller\Controller;
use Cake\Controller\ComponentRegistry;
use Unimatrix\Backend\Controller\Component\FlashComponent;

class FlashComponentTest extends TestCase
{
    public function testAutomaticallySetPluginElement() {
        $message = 'Test flash message';
        $controller = new Controller();
        $registry = new ComponentRegistry($controller);
        $component = new FlashComponent($registry);
        $component->success($message);
        $result = $component->getController()->getRequest()->getSession()->read('Flash.flash')[0];

        $this->assertSame($message, $result['message']);
        $this->assertSame('Unimatrix/Backend.Flash/success', $result['element']);
    }
}
