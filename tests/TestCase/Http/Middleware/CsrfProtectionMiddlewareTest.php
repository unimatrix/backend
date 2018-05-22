<?php

namespace Unimatrix\Backend\Test\TestCase\Http\Middleware;

use Cake\TestSuite\TestCase;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\I18n\Time;
use Cake\Http\Middleware\CsrfProtectionMiddleware as CakeCsrfProtectionMiddleware;

class CsrfProtectionMiddlewareTest extends TestCase
{
    public function testSomething() {
        $this->assertTrue(true);
    }
}
