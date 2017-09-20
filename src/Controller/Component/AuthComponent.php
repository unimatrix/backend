<?php

namespace Unimatrix\Backend\Controller\Component;

use DateTime;
use Cake\Core\Configure;
use Cake\Http\Cookie\Cookie;
use Cake\Http\Cookie\CookieCollection;
use Cake\Controller\ComponentRegistry;
use Cake\Controller\Component\AuthComponent as CakeAuthComponent;

/**
 * Auth component
 * Overwrite default Auth Component and add backend config and autologin
 *
 * @author Flavius
 * @version 1.2
 */
class AuthComponent extends CakeAuthComponent
{
    /**
     * Our own config
     * @var array
     */
    protected $_unimatrixConfig = [
        'authenticate' => ['Unimatrix/Backend.Backend'],
        'loginAction' => [
            'controller' => 'Login',
            'action' => 'index',
            'plugin' => 'Unimatrix/Backend'
        ]
    ];

    /**
     * {@inheritDoc}
     * @see \Cake\Controller\Component\AuthComponent::__construct()
     */
    public function __construct(ComponentRegistry $registry, array $config = []) {
        // add our own config
        $config += $this->_unimatrixConfig;
        $config['cookieName'] = Configure::read('Backend.credentials.cookie', 'backend_credentials_remember');

        // continue as normal
        parent::__construct($registry, $config);
    }

    /**
     * {@inheritDoc}
     * @see \Cake\Controller\Component\AuthComponent::initialize()
     */
    public function initialize(array $config) {
        parent::initialize($config);

        // autologin based on cookie
        $request = $this->getController()->request;
        $cookies = (new CookieCollection())->createFromServerRequest($request);
        if(is_null($this->user()) && $cookies->has($this->_config['cookieName'])) {
            // perform login
            $result = $this->login();
            if($result) {
                $this->getController()->Flash->set('You have successfully auto-logged in as ' . ucfirst($this->user('username')));
                return $this->getController()->redirect($request->getRequestTarget());

            // bad cookie
            } else $this->getController()->response = $this->getController()->response->withExpiredCookie(new Cookie($this->_config['cookieName']));
        }

        // set auth to template
        $this->getController()->set('auth', $this->user());

        // allow public logout
        $this->allow('logout');
    }

    /**
     * Perform login
     * @return string|boolean
     */
    public function login() {
        // check for match
        $user = $this->identify();
        if($user) {
            // login user
            $this->setUser($user);

            // write cookie if remember me was checked
            if((bool)$this->getController()->request->getData('remember', 0)) {
                // build cookie
                $this->getController()->response = $this->getController()->response->withCookie(
                    (new Cookie($this->_config['cookieName']))
                        ->withValue($user)
                        ->withExpiry(new DateTime('+1 month'))
                        ->withSecure(env('HTTPS'))
                        ->withHttpOnly(Configure::read('Backend.security.enabled') ?: false)
                );
            }

            // redirect to backend (or wherever he was) after login
            $redirect = $this->redirectUrl();
            return $redirect == '/' ? '/' . $this->getController()->request->getParam('prefix') : $redirect;

        // user could not be identified
        } else return false;
    }

    /**
     * {@inheritDoc}
     * @see \Cake\Controller\Component\AuthComponent::logout()
     */
    public function logout(){
        // delete cookie
        $this->getController()->response = $this->getController()->response->withExpiredCookie(new Cookie($this->_config['cookieName']));

        // continue as normal
        return parent::logout();
    }

    /**
     * {@inheritDoc}
     * @see \Cake\Controller\Component\AuthComponent::setUser()
     */
    public function setUser($user) {
        // strip the password :)
        unset($user['password']);

        // continue as normal;
        parent::setUser($user);
    }
}
