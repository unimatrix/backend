<?php

use Cake\Core\Plugin;
use Cake\Core\Configure;
use Cake\Event\EventManager;
use Cake\Http\Middleware\CsrfProtectionMiddleware;
use Cake\Http\Middleware\EncryptedCookieMiddleware;
use Unimatrix\Backend\Routing\Middleware\WysiwygMiddleware;

// load Unimatrix Cake
Plugin::load('Unimatrix/Cake', ['bootstrap' => true]);

// not backend? don't continue
if(Configure::read('Backend') && explode('/', env('REQUEST_URI'))[1] !== 'backend')
    return;

// attach middleware
EventManager::instance()->on('Server.buildMiddleware', function ($event, $queue) {
    // WysiwygMiddleware
    $queue->insertBefore('Cake\Routing\Middleware\AssetMiddleware', WysiwygMiddleware::class);

    // EncryptedCookieMiddleware
    if(Configure::check('Security.cookie'))
        $queue->add(new EncryptedCookieMiddleware([Configure::read('Backend.credentials.cookie', 'backend_credentials_remember')], Configure::read('Security.cookie')));

    // CsrfProtectionMiddleware
    if(Configure::read('Backend.security.enabled'))
        $queue->add(new CsrfProtectionMiddleware([
            'httpOnly' => true,
            'secure' => env('HTTPS'),
            'cookieName' => 'backend_csrf_token',
        ]));
});
