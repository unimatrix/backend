<?php

use Cake\Routing\Router;
use Cake\Routing\RouteBuilder;
use Cake\Routing\Route\DashedRoute;

// backend
Router::prefix('backend', function(RouteBuilder $routes) {
    $routes->connect('/login', ['controller' => 'Login', 'action' => 'index', 'plugin' => 'Unimatrix/Backend']);
    $routes->connect('/logout', ['controller' => 'Login', 'action' => 'logout', 'plugin' => 'Unimatrix/Backend']);

    $routes->fallbacks(DashedRoute::class);
});
