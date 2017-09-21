<?php

namespace Unimatrix\Backend\Http\Middleware;

use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\I18n\Time;
use Cake\Http\Middleware\CsrfProtectionMiddleware as CakeCsrfProtectionMiddleware;

/**
 * CSRF Protection
 * Wrapper of the original cake CSRF Protection middleware with the addition
 * of adding the `backend` configuration and prefix to the cookie path
 *
 * @author Flavius
 * @version 1.0
 */
class CsrfProtectionMiddleware extends CakeCsrfProtectionMiddleware
{
    /**
     * Constructor
     *
     * @param array $config Config options. See $_defaultConfig for valid keys.
     */
    public function __construct(array $config = []) {
        parent::__construct($config + [
            'httpOnly' => true,
            'secure' => env('HTTPS'),
            'cookieName' => 'backend_csrf_token'
        ]);
    }

    /**
     * {@inheritDoc}
     * @see \Cake\Http\Middleware\CsrfProtectionMiddleware::_addTokenCookie()
     */
    protected function _addTokenCookie($token, ServerRequest $request, Response $response) {
        return $response->withCookie($this->_config['cookieName'], [
            'value' => $token,
            'expire' => (new Time($this->_config['expiry']))->format('U'),
            'path' => $request->getAttribute('webroot') . $request->getParam('prefix'),
            'secure' => $this->_config['secure'],
            'httpOnly' => $this->_config['httpOnly'],
        ]);
    }
}
