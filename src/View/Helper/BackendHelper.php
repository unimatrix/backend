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
 * @version 1.3
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
            'config' => [
                'html' => [
                    'doRemoveOmittedHtmlTags' => false
                ],
                'css' => [],
                'js' => []
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
     * Outputs value of an entity with some caveats
     * - field isnt currently search and must be truncated? do that
     * - don't perform truncating in case field is search to display the hightlight
     *
     * @param Cake\ORM\Entity $entity
     * @param string $field
     * @param array $options An array of options.
     *
     * @return string
     */
    public function search(Entity $entity, $field, array $options = []) {
        // options
        $defaults = [
            'truncate' => false,
            'html' => false
        ];

        // overwrite defaults with options values
        $options += $defaults;

        // handle value
        $value = null;
        if($entity->has($field))
            $value = $entity->get($field);

        // field isn't searched and must be truncated?
        $highlight = $this->getView()->get('highlight', []);
        if(!isset($highlight[$field]) && $options['truncate'])
            return $this->Text->truncate($value, $options['truncate'], ['html' => true]);

        // return highlighted
        return $this->highlight($value, $field, $options['html']);
    }

    /**
     * Highlights the text
     * - can also be used standalone to highlight composed values
     * from different fields (assuming their fields were searched)
     *
     * @param string $value
     * @param string $field
     * @return string
     */
    public function highlight($value, $field, $html = false) {
        $highlight = $this->getView()->get('highlight', []);
        if(!isset($highlight[$field]))
            return $value;

        return Lexicon::highlight($value, $highlight[$field], $html);
    }
}
