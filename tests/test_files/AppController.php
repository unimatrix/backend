<?php

namespace App\Controller;

use Cake\Controller\Controller;

// Application Controller
class AppController extends Controller
{
    public function initialize() {
        parent::initialize();

        // no prefix? frontend
        if(!$this->getRequest()->getParam('prefix'))
            $this->loadComponent('Unimatrix/Frontend.Frontend');

        // got backend prefix? backend
        if($this->getRequest()->getParam('prefix') === 'backend')
            $this->loadComponent('Unimatrix/Backend.Backend');
    }
}
