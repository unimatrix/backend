<?php

use Cake\Core\Plugin;
use Cake\Core\Configure;
use Cake\Event\EventManager;
use Unimatrix\Backend\Routing\Middleware\WysiwygMiddleware;
use Unimatrix\Backend\Http\Middleware\CsrfProtectionMiddleware;
use Unimatrix\Backend\Http\Middleware\EncryptedCookieMiddleware;
use Unimatrix\Cake\Error\Middleware\EmailErrorHandlerMiddleware;

// load Unimatrix Cake
Plugin::load('Unimatrix/Cake');

// cli or not backend? don't continue
if(PHP_SAPI === 'cli' || Configure::read('Backend') && explode('/', env('REQUEST_URI'))[1] !== 'backend')
    return;

// attach middleware
EventManager::instance()->on('Server.buildMiddleware', function ($event, $queue) {
    // EmailErrorHandlerMiddleware
    $queue->insertAt(0, EmailErrorHandlerMiddleware::class);

    // WysiwygMiddleware
    $queue->insertBefore('Cake\Routing\Middleware\AssetMiddleware', WysiwygMiddleware::class);

    // EncryptedCookieMiddleware
    if(Configure::check('Backend.security.salt') && strlen(Configure::read('Backend.security.salt')) === 64)
        $queue->add(EncryptedCookieMiddleware::class);

    // CsrfProtectionMiddleware
    if(Configure::read('Backend.security.enabled'))
        $queue->add(CsrfProtectionMiddleware::class);
});
