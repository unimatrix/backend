<?php

namespace Unimatrix\Backend\Controller\Component;

use Cake\Core\Configure;
use Cake\Controller\ComponentRegistry;
use Cake\Controller\Component\AuthComponent as CakeAuthComponent;

/**
 * Auth component
 * Overwrite default Auth Component and add backend config and autologin
 *
 * @author Flavius
 * @version 0.1
 */
class AuthComponent extends CakeAuthComponent
{
    /**
     * Holds the controller
     * @var \Cake\Controller\Controller
     */
    protected $_Ctrl;

    /**
     * Our own config
     * @var array
     */
    protected $_unimatrixConfig = [
        'authenticate' => ['Unimatrix/Backend.Backend'],
        'cookieName' => 'Backend.credentials',
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

        // continue as normal
        parent::__construct($registry, $config);
    }

    /**
     * {@inheritDoc}
     * @see \Cake\Controller\Component\AuthComponent::initialize()
     */
    public function initialize(array $config) {
        parent::initialize($config);

        // set controller
        $this->_Ctrl = $this->getController();

        // load cookie
        $this->_Ctrl->loadComponent('Cookie', [
            'httpOnly' => Configure::read('Backend.security.enabled') ?: false,
            'secure' => env('HTTPS')
        ]);

        // not logged in but got cookie? (autologin)
        $request = $this->_Ctrl->request;
        $cookie = $this->_Ctrl->Cookie->read($this->_config['cookieName']);
        if(is_null($this->user()) && $cookie) {
            // disable csrf & security (we're modifying the request below soooooo yeah)
            if(Configure::read('Backend.security.enabled')) {
                $this->_Ctrl->eventManager()->off($this->_Ctrl->Csrf);
                $this->_Ctrl->Security->config('unlockedActions', [$request->getParam('action')]);
            }

            // update request with data from cookie :)
            $this->_Ctrl->request = $request->withParsedBody($cookie);

            // perform login
            $result = $this->login();
            if($result) {
                $this->_Ctrl->Flash->set('You have successfully auto-logged in as ' . ucfirst($this->user('username')));
                return $this->_Ctrl->redirect($request->getRequestTarget());
            } else $this->_Ctrl->Cookie->delete($this->_config['cookieName']);
        }

        // set auth to template
        $this->_Ctrl->set('auth', $this->user());

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
            if((bool)$this->_Ctrl->request->getData('remember', 0)) {
                $this->_Ctrl->Cookie->configKey($this->_config['cookieName'], ['expires' => '+1 month']);
                $this->_Ctrl->Cookie->write($this->_config['cookieName'], $user);
            }

            // redirect to backend (or wherever he was) after login
            $redirect = $this->redirectUrl();
            return $redirect == '/' ? '/' . $this->_Ctrl->request->getParam('prefix') : $redirect;

        // user could not be identified
        } else return false;
    }

    /**
     * {@inheritDoc}
     * @see \Cake\Controller\Component\AuthComponent::logout()
     */
    public function logout(){
        // delete cookie
        $this->_Ctrl->Cookie->delete($this->_config['cookieName']);

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
