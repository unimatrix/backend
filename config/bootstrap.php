<?php

use Cake\Core\Plugin;
use Cake\Event\EventManager;
use Unimatrix\Backend\Routing\Middleware\WysiwygMiddleware;

// load Unimatrix Cake
Plugin::load('Unimatrix/Cake', ['bootstrap' => true]);

// attach WysiwygMiddleware
EventManager::instance()->on('Server.buildMiddleware', function ($event, $queue) {
    $queue->insertBefore('Cake\Routing\Middleware\AssetMiddleware', WysiwygMiddleware::class);
});
