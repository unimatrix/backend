<?php

namespace Unimatrix\Backend\View\Widget;

use Cake\View\View;
use Cake\View\Widget\BasicWidget;
use Cake\View\Form\ContextInterface;

/**
 * Wysiwyg (What You See Is What You Get)
 * This widget is used in conjunction with CKEditor and CKFinder
 *
 * Example:
 * ---------------------------------------------------------------------------------
 * echo $this->Form->control('body', ['type' => 'wysiwyg']);
 *
 * @author Flavius
 * @version 0.1
 */
class WysiwygWidget extends BasicWidget
{
    // extra file prerequisites
    private $prerequisites = [
        // javascript
        'script' => [
            'Unimatrix/Backend.scripts/ckeditor/ckeditor.min.js',
            'Unimatrix/Backend.scripts/ckfinder/ckfinder.min.js',
            'Unimatrix/Backend.widgets/wysiwyg.js',
        ]
    ];

    /**
     * Load prerequisites
     * @param View $view - The view object
     * @return string - CK paths
     */
    public function require(View $view) {
        // load prerequisites
        $ckeditor = null;
        $ckfinder = null;
        foreach($this->prerequisites as $type => $files) {
            foreach($files as $file) {
                if(strpos($file, 'ckeditor') !== false) $ckeditor = $file;
                if(strpos($file, 'ckfinder') !== false) $ckfinder = $file;
            }
            $view->Minify->$type($files);
        }

        // figure out ckeditor and ckfinder path
        $path = null;
        if(!is_null($ckeditor) && !is_null($ckfinder)) {
            $editorPath = dirname($view->Url->script($ckeditor));
            $finderPath = dirname($view->Url->script($ckfinder));
            $path = $view->Minify->inline('script', "var CKEDITOR_BASEPATH = '{$editorPath}/', CKFINDER_BASEPATH = '{$finderPath}/';", true);
        }

        // return ckeditor and ckfinder paths
        return $path;
    }

    /**
     * Render a text widget or other simple widget like email/tel/number.
     *
     * This method accepts a number of keys:
     *
     * - `name` The name attribute.
     * - `val` The value attribute.
     * - `escape` Set to false to disable escaping on all attributes.
     *
     * Any other keys provided in $data will be converted into HTML attributes.
     *
     * @param array $data The data to build an input with.
     * @param \Cake\View\Form\ContextInterface $context The current form context.
     * @return string
     */
    public function render(array $data, ContextInterface $context) {
        // defaults
        $data += [
            'val' => '',
            'name' => '',
            'escape' => true,
            'rows' => 5,
            'templateVars' => []
        ];

        // require prerequisites
        $ckpath = $this->require($data['view']);
        unset($data['view']);

        // create the textarea
        $textarea = $this->_templates->format('textarea', [
            'name' => $data['name'],
            'value' => $data['escape'] ? h($data['val']) : $data['val'],
            'templateVars' => $data['templateVars'],
            'attrs' => $this->_templates->formatAttributes(
                $data,
                ['name', 'val', 'type']
            )
        ]);

        // render
        return "<div class='wysiwyg-widget'>" . $textarea . $ckpath . '</div>';
    }
}
