<?php

namespace Unimatrix\Backend\View\Widget;

use Cake\View\View;
use Cake\View\Widget\BasicWidget;
use Cake\View\Form\ContextInterface;
use Cake\Utility\Text;
use RuntimeException;

/**
 * Media
 * This widget is used in conjunction with ckfinder to select or upload and select images
 * Note: In case of multiple, value must be array and the return will also be array
 *
 * Example:
 * ---------------------------------------------------------------------------------
 * echo $this->Form->control('media', ['type' => 'media']);
 * echo $this->Form->control('media2', ['type' => 'media', 'multiple' => true]);
 *
 * @author Flavius
 * @version 1.2
 */
class MediaWidget extends BasicWidget
{
    // extra file prerequisites
    private $prerequisites = [
        // css
        'style' => [
            'Unimatrix/Backend.widgets/media.css',
        ],

        // javascript
        'script' => [
            'Unimatrix/Backend.scripts/ckfinder/ckfinder.min.js',
            'Unimatrix/Backend.widgets/media.js',
        ]
    ];

    // the empty value
    private $emptyValue = '_to_empty_array_';

    /**
     * Load prerequisites
     * @param View $view - The view object
     * @return string - CKfinder path
     */
    public function require(View $view) {
        // load prerequisites
        $ckfinder = null;
        foreach($this->prerequisites as $type => $files) {
            foreach($files as $file)
                if(strpos($file, 'ckfinder') !== false) $ckfinder = $file;

            $view->Minify->$type($files);
        }

        // figure out ckeditor and ckfinder path
        $path = null;
        if(!is_null($ckfinder))
            $path = $view->Minify->inline('script', "var CKFINDER_BASEPATH = '" . dirname($view->Url->script($ckfinder)). "/';", true);

        // return ckeditor and ckfinder paths
        return $path;
    }

    /**
     * Create item
     * @param View $view
     * @param string $value
     * @param bool $new
     * @return string
     */
    protected function item(View $view, $value, $new = false) {
        // create item
        $out = '<media id="' . Text::uuid(). '"' . ($new ? " class='new'" : null) . '>';
        $out .= $view->Html->image($new ? 'Unimatrix/Backend.widgets/media-plus.png' : $value);
        $out .= $view->Html->link('<i class="fa fa-eye" aria-hidden="true"></i> ' . __d('Unimatrix/backend', 'Full Image'), $new ? '#' : $value, ['escape' => false, 'target' => '_blank']);
        $out .= '<i class="fa fa-times" aria-hidden="true"></i>';
        $out .= '</media>';

        // return item
        return $out;
    }

    /**
     * Create input
     * @param string $name
     * @param string $value
     * @return string
     */
    protected function input($name, $value = false) {
        // default empty value
        if(!$value)
            $value = $this->emptyValue;

        // return input
        return $this->_templates->format('input', [
            'name' => $name,
            'type' => 'hidden',
            'attrs' => $this->_templates->formatAttributes(['value' => $value]),
        ]);
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
            'multiple' => false,
            'escape' => true,
            'templateVars' => [],
        ];

        // visual only
        $inputs = null;
        $multiple = null;
        $hval = $data['val'];
        $list = '<div class="list">';

        // multiple
        if($data['multiple']) {
            // make sure value is array
            if(is_null($hval))
                $hval = [];
            if(!is_array($hval))
                throw new RuntimeException("Media Widget: '{$data['name']}' value is not an array");

            // transform into array
            $data['name'] = $data['name'] . '[]';
            $multiple = ' multiple';

            // loop through existing data and add items
            if($hval) {
                foreach($hval as $item) {
                    $list .= $this->item($data['view'], $item, false);
                    $inputs .= $this->input($data['name'], $item);
                }
            } else $inputs .= $this->input($data['name']);

            // add the empty item
            $list .= $this->item($data['view'], null, true);

        // solo
        } else {
            $list .= $this->item($data['view'], $hval, $hval ? false : true);
            $inputs .= $this->input($data['name'], $hval);
        }

        // visual only
        $list .= '</div>';

        // require prerequisites
        $finderPath = $this->require($data['view']);
        unset($data['view']);

        // return the actual template for this input type
        return "<div class='media-widget{$multiple}'>" . $list . $inputs . $finderPath . '</div>';
    }
}
