<?php

namespace Unimatrix\Backend\Test\TestCase\Http\Middleware;

use Cake\TestSuite\TestCase;
use Cake\Core\Configure;
use Cake\Http\Response;
use Cake\Http\Cookie\CookieCollection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Cake\Http\Middleware\EncryptedCookieMiddleware as CakeEncryptedCookieMiddleware;

class EncryptedCookieMiddlewareTest extends TestCase
{
    public function testSomething() {
        $this->assertTrue(true);
    }
}
