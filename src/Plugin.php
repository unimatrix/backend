<?php

namespace Unimatrix\Backend;

use Cake\Core\Configure;
use Cake\Core\BasePlugin;
use Cake\Routing\RouteBuilder;
use Cake\Routing\Route\DashedRoute;
use Cake\Http\ServerRequestFactory;
use Unimatrix\Backend\Routing\Middleware\WysiwygMiddleware;
use Unimatrix\Backend\Http\Middleware\CsrfProtectionMiddleware;
use Unimatrix\Backend\Http\Middleware\EncryptedCookieMiddleware;

/**
 * Backend Plugin
 *
 * @author Flavius
 * @version 1.0
 */
class Plugin extends BasePlugin
{
    /**
     * Should this plugin be activated
     * @return bool
     */
    protected function inEffect() {
        $url = explode('/', ServerRequestFactory::fromGlobals()->getPath());

        // not backend? abort
        if(Configure::read('Backend') && !($url[1] === 'backend' || ($url[1] === 'unimatrix' && $url[2] === 'backend')))
            return false;

        // valid
        return true;
    }

    /**
     * {@inheritDoc}
     * @see \Cake\Core\BasePlugin::middleware()
     */
    public function middleware($middleware) {
        if($this->inEffect()) {
            // WysiwygMiddleware
            $middleware->insertBefore('Cake\Routing\Middleware\AssetMiddleware',
                WysiwygMiddleware::class);

            // EncryptedCookieMiddleware
            if(Configure::check('Backend.security.salt') && strlen(Configure::read('Backend.security.salt')) === 64)
                $middleware->add(EncryptedCookieMiddleware::class);

            // CsrfProtectionMiddleware
            if(Configure::read('Backend.security.enabled'))
                $middleware->add(CsrfProtectionMiddleware::class);
        }

        return $middleware;
    }

    /**
     * {@inheritDoc}
     * @see \Cake\Core\BasePlugin::routes()
     */
    public function routes($routes) {
        $routes->prefix('backend', function(RouteBuilder $routes) {
            $routes->connect('/login', ['controller' => 'Login', 'action' => 'index', 'plugin' => 'Unimatrix/Backend']);
            $routes->connect('/logout', ['controller' => 'Login', 'action' => 'logout', 'plugin' => 'Unimatrix/Backend']);

            $routes->fallbacks(DashedRoute::class);
        });
    }
}
