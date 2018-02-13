<?php

namespace Unimatrix\Backend\View\Helper;

use Cake\View\Helper;
use Cake\Event\Event;
use Cake\ORM\Entity;
use Unimatrix\Cake\Lib\Lexicon;

/**
 * Backend Helper
 * This helper loads all other necesary stuff for the backend,
 * it also handles some custom backend logic and template correction
 *
 * @author Flavius
 * @version 1.2
 */
class BackendHelper extends Helper {
    // load other helpers
    public $helpers = ['Text'];

    // default config
    protected $_defaultConfig = [
        'Minify' => [
            'compress' => [
                'html' => true,
                'css' => true,
                'js' => true
            ],
            'paths' => [
                'css' => '/cache-css',
                'js' => '/cache-js'
            ]
        ],
        'Form' => ['widgets' => [
            'tag' => ['Unimatrix/Backend.Tag'],
            'calendar' => ['Unimatrix/Backend.Calendar'],
            'wysiwyg' => ['Unimatrix/Backend.Wysiwyg'],
            'picky' => ['Unimatrix/Backend.Picky'],
            'media' => ['Unimatrix/Backend.Media'],
            'upload' => ['Unimatrix/Backend.Upload'],
            'stepper' => ['Unimatrix/Backend.Stepper']
        ]]
    ];

    /**
     * {@inheritDoc}
     * @see \Cake\View\Helper::initialize()
     */
    public function initialize(array $config) {
        parent::initialize($config);

        // we need this
        $view = $this->getView();

        // load required helpers
        $view->loadHelper('Unimatrix/Cake.Debug');
        $view->loadHelper('Unimatrix/Cake.Minify', $this->_config['Minify']);
        $view->loadHelper('Unimatrix/Cake.Form', $this->_config['Form']);
    }

    /**
     * beforeRender
     * @param Event $event
     * @param string $viewFile
     */
    public function beforeRender(Event $event, $viewFile) {
        // switch layout to backend
        $this->getView()->setLayout('Unimatrix/Backend.backend');
    }

    /**
     * In case of search, highlight the text
     * @param Cake\ORM\Entity $entity
     * @param string $field
     * @param bool | integer $truncate
     * @return string
     */
    public function search(Entity $entity, $field, $truncate = false) {
        // handle value
        $value = null;
        if($entity->has($field))
            $value = $entity->get($field);

        // no highlight for this field? return truncated text (if truncated)
        $highlight = $this->getView()->get('highlight') ? $this->getView()->get('highlight') : [];
        if(!isset($highlight[$field])) {
            if($truncate)
                $value = $this->Text->truncate($value, $truncate, ['html' => true]);

        // otherwise return highlighted without truncating the text
        } else $value = Lexicon::highlight($value, $highlight[$field]);

        // return teh value
        return $value;
    }
}
