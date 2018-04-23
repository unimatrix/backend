<?php

namespace Unimatrix\Backend\Controller\Backend;

use Unimatrix\Backend\Controller\AppController;

/**
 * Login ctrl
 * Handles login and logout
 *
 * @author Flavius
 * @version 1.0
 */
class LoginController extends AppController
{
    // login
    public function index() {
        // logout if trying to login while already logged in
        if($this->Auth->user())
            return $this->redirect(['action' => 'logout']);

        // perform login
        if($this->getRequest()->is('post')) {
            $result = $this->Auth->login();
            if($result) {
                $this->Flash->success(__d('Unimatrix/backend', 'You have successfully logged in as {0}.', ucfirst($this->Auth->user('username'))));
                return $this->redirect($result);
            } else $this->Flash->error(__d('Unimatrix/backend', 'The username or password you entered is incorrect.'));
        }
    }

    // logout
    public function logout() {
        // not logged in?
        if(!$this->Auth->user())
            return $this->redirect(['action' => 'index']);

        // perform logout
        return $this->redirect($this->Auth->logout());
    }
}
