<?php

namespace Unimatrix\Backend\View\Helper;

use Cake\View\Helper;
use Cake\Event\Event;
use Cake\ORM\Entity;
use Unimatrix\Cake\Lib\Lexicon;

/**
 * Admin Helper
 * This helper loads all other necesary stuff for the backend,
 * it also handles some custom backend logic and template correction
 *
 * @author Flavius
 * @version 0.1
 */
class AdminHelper extends Helper {
    // load other helpers
    public $helpers = ['Text'];

    /**
     * {@inheritDoc}
     * @see \Cake\View\Helper::initialize()
     */
    public function initialize(array $config) {
        parent::initialize($config);

        // load required helpers
        $this->_View->loadHelper('Unimatrix/Cake.Minify');
        $this->_View->loadHelper('Unimatrix/Cake.Debug');
        $this->_View->loadHelper('Unimatrix/Backend.Form', ['widgets' => [
            'tag' => ['Unimatrix/Backend.Tag'],
            'moment' => ['Unimatrix/Backend.Moment'],
            'wysiwyg' => ['Unimatrix/Backend.Wysiwyg'],
            'picky' => ['Unimatrix/Backend.Picky'],
            'media' => ['Unimatrix/Backend.Media'],
            'upload' => ['Unimatrix/Backend.Upload']
        ]]);
    }

    /**
     * beforeRender
     * @param Event $event
     * @param string $viewFile
     */
    public function beforeRender(Event $event, $viewFile) {
        // switch layout to backend
        $this->_View->layout('Unimatrix/Backend.backend');
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
        $highlight = $this->_View->get('highlight') ? $this->_View->get('highlight') : [];
        if(!isset($highlight[$field])) {
            if($truncate)
                $value = $this->Text->truncate($value, $truncate, ['html' => true]);

        // otherwise return highlighted without truncating the text
        } else $value = Lexicon::highlight($value, $highlight[$field]);

        // return teh value
        return $value;
    }
}
