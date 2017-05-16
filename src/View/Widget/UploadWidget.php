<?php

namespace Unimatrix\Backend\View\Widget;

use Cake\View\View;
use Cake\View\Widget\FileWidget;
use Cake\View\Form\ContextInterface;

/**
 * Upload
 * This widget is used in conjunction with the uploadable behavior
 *
 * Example:
 * ---------------------------------------------------------------------------------
 * echo $this->Form->control('file', ['type' => 'upload']);
 *
 * IMPORTANT:
 * =================================================================================
 * Make sure you also add the Uploadable Behavior
 * in your Model/Table/TableNameTable.php
 * ---------------------------------------------------------------------------------
 * $this->addBehavior('Unimatrix/Cake.Uploadable', [
 *     'fields' => [
 *         'file' => 'img/:model/:uuid'
 *     ]
 * ]);
 *
 * Field identifiers:
 * -------------------------------------------------------
 * :model: The model name
 * :uuid: A random and unique identifier UUID type
 * :md5: A random and unique identifier with 32 characters.
 *
 * Validation:
 * ---------------------------------------------------------------------------------
 * $validator
 *     ->requirePresence('file', 'create')
 *     ->allowEmpty('file', 'update');
 *
 * @author Flavius
 * @version 1.0
 */
class UploadWidget extends FileWidget
{
    // extra file prerequisites
    private $prerequisites = [
        // css
        'style' => [
            'Unimatrix/Backend.widgets/upload.css',
        ]
    ];

    /**
     * Load prerequisites
     * @param View $view - The view object
     */
    public function require(View $view) {
        foreach($this->prerequisites as $type => $files)
            $view->Minify->$type($files);
    }

    /**
     * Render a file upload form widget.
     * @param array $data The data to build a file input with.
     * @param \Cake\View\Form\ContextInterface $context The current form context.
     * @return string HTML elements.
     */
    public function render(array $data, ContextInterface $context) {
        // default options
        $data += [
            'name' => '',
            'escape' => true,
            'templateVars' => [],
        ];

        // preview value
        $preview = null;
        if($data['val'] && !is_array($data['val'])) {
            $preview = "<div class='preview'>{$data['view']->Html->link($data['view']->Html->image($data['val']), $data['val'], ['target' => '_blank', 'escape' => false])}</div>";
            unset($data['val']);
        }

        // require prerequisites
        $this->require($data['view']);
        unset($data['view']);

        // create the file
        $file = $this->_templates->format('file', [
            'name' => $data['name'],
            'templateVars' => $data['templateVars'],
            'attrs' => $this->_templates->formatAttributes(
                $data,
                ['name', 'val']
            )
        ]);

        // return the actual template for this input type
        return "<div class='upload-widget'>" . $preview . $file. '</div>';
    }
}
