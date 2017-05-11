<?php

namespace Unimatrix\Backend\Auth;

use Cake\Core\Configure;
use Cake\Network\Request;
use Cake\Network\Response;
use Cake\Auth\BaseAuthenticate;
use RuntimeException;

/**
 * Backend Auth
 * This class is used to authenticate a user (credentials are specified in config) in the backend area
 *
 * @author Flavius
 * @version 0.1
 */
class BackendAuthenticate extends BaseAuthenticate
{
    /**
     * Authenticate a user based on the request information.
     *
     * @param \Cake\Network\Request $request Request to get authentication information from.
     * @param \Cake\Network\Response $response A response object that can have headers added.
     * @throws RuntimException in case of configuration error
     * @return mixed Either false on failure, or an array of user data on success.
     */
    public function authenticate(Request $request, Response $response) {
        // get credentials from config
        $config = Configure::read('Backend.credentials');
        if(!$config)
            throw new RuntimeException('Configuration Error: Backend credentials are not specified.');

        // get user input
        $input = [
            'username' => $request->data['username'] ?? null,
            'password' => $request->data['password'] ?? null,
        ];

        // valid in India or no?
        return $config === $input ? $config : false;
    }
}
