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
 * @version 1.4
 */
class AuthComponent extends CakeAuthComponent
{
    // the controller
    private $ctrl;

    /**
     * {@inheritDoc}
     * @see \Cake\Controller\Component\AuthComponent::__construct()
     */
    public function __construct(ComponentRegistry $registry, array $config = []) {
        // set some config stuff
        $config['cookieName'] = Configure::read('Backend.credentials.cookie', 'backend_credentials_remember');
        $config['authError'] = __d('Unimatrix/backend', 'You are not authorized to access that location.');

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
        $this->ctrl = $this->getController();

        // autologin based on cookie
        if(is_null($this->user()) && $this->ctrl->Cookie->check($this->_config['cookieName'])) {
            // perform login
            $result = $this->login();
            if($result) {
                $this->ctrl->Flash->set(__d('Unimatrix/backend', 'You have successfully auto-logged in as {0}.', ucfirst($this->user('username'))));
                return $this->ctrl->redirect($this->ctrl->getRequest()->getRequestTarget());

            // bad cookie
            } else $this->ctrl->Cookie->delete($this->_config['cookieName']);
        }

        // set auth to template
        $this->ctrl->set('auth', $this->user());

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
            if((bool)$this->ctrl->getRequest()->getData('remember', 0))
                $this->ctrl->Cookie->write($this->_config['cookieName'], $user, ['expire' => '+1 month']);

            // redirect to backend (or wherever he was) after login
            $redirect = $this->redirectUrl();
            return $redirect == '/' ? '/' . $this->ctrl->getRequest()->getParam('prefix') : $redirect;

        // user could not be identified
        } else return false;
    }

    /**
     * {@inheritDoc}
     * @see \Cake\Controller\Component\AuthComponent::logout()
     */
    public function logout(){
        // delete cookie
        $this->ctrl->Cookie->delete($this->_config['cookieName']);

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
