<?php

namespace Unimatrix\Backend\Auth;

use Cake\Core\Configure;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\Http\Cookie\CookieCollection;
use Cake\Auth\BaseAuthenticate;
use RuntimeException;

/**
 * Backend Auth
 * This class is used to authenticate a user (credentials are specified in config) in the backend area
 * - checks against form input
 * - checks against cookie
 *
 * @author Flavius
 * @version 1.1
 */
class BackendAuthenticate extends BaseAuthenticate
{
    /**
     * {@inheritDoc}
     * @see \Cake\Auth\BaseAuthenticate::authenticate()
     * @throws RuntimeException in case of configuration error
     */
    public function authenticate(ServerRequest $request, Response $response) {
        // get credentials from config
        $config = Configure::read('Backend.credentials');
        if(!$config)
            throw new RuntimeException('Configuration Error: Backend credentials are not specified.');

        // get cookies
        $cookies = (new CookieCollection())->createFromServerRequest($request);
        $name = Configure::read('Backend.credentials.cookie', 'backend_credentials_remember');

        // build check
        $check = [
            'username' => $config['username'],
            'password' => $config['password']
        ];

        // build against
        $against = [
            'username' => $request->getData('username', $cookies->has($name) ? $cookies->get($name)->read('username') : null),
            'password' => $request->getData('password', $cookies->has($name) ? $cookies->get($name)->read('password') : null),
        ];

        // valid in India or no?
        return $check === $against ? $check : false;
    }
}
