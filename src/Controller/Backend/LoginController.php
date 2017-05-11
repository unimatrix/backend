<?php

namespace Unimatrix\Backend\Controller\Backend;

use Unimatrix\Backend\Controller\AppController;

/**
 * Login ctrl
 * Handles login and logout
 *
 * @author Flavius
 * @version 0.1
 */
class LoginController extends AppController
{
    // login
    public function index() {
        // logout if trying to login while already logged in
        if($this->Auth->user())
            return $this->redirect(['action' => 'logout']);

        // perform login
        if($this->request->is('post')) {
            $result = $this->Auth->login();
            if($result) {
                $this->Flash->success('You have successfully logged in as ' . ucfirst($this->Auth->user('username')));
                return $this->redirect($result);
            } else $this->Flash->error($this->Auth->config('authError'));
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
