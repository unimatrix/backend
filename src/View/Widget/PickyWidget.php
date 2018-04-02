<?php

namespace Unimatrix\Backend\View\Widget;

use Cake\View\View;
use Cake\View\Widget\BasicWidget;
use Cake\View\Form\ContextInterface;
use RuntimeException;

/**
 * Picky
 * This widget is used to select between multiple choices
 * Note: array only (values, list)
 *
 * Example:
 * ---------------------------------------------------------------------------------
 * echo $this->Form->control('pick', ['type' => 'picky', 'list' => ['item1', 'item2', 'item3', 'item4']])
 *
 * @author Flavius
 * @version 1.1
 */
class PickyWidget extends BasicWidget
{
    // extra file prerequisites
    private $prerequisites = [
        // css
        'style' => [
            'Unimatrix/Backend.widgets/picky.css',
        ],

        // javascript
        'script' => [
            'Unimatrix/Backend.widgets/picky.js',
        ]
    ];

    // the empty value
    private $emptyValue = '_to_empty_array_';

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
            'val' => null,
            'escape' => true,
            'templateVars' => []
        ];

        // make null value into empty array
        if(is_null($data['val']))
            $data['val'] = [];

        // picky errors? throw exceptions
        if(!$data['list'])
            throw new RuntimeException("Picky Widget: '{$data['name']}' must contain a list of an array with values to choose from");
        if(!is_array($data['list']))
            throw new RuntimeException("Picky Widget: '{$data['name']}' list is not an array");
        if(!is_array($data['val']))
            throw new RuntimeException("Picky Widget: '{$data['name']}' value is not an array");

        // require prerequisites
        $this->require($data['view']);
        unset($data['view']);

        // create inputs
        $elements = null;
        if($data['val']) {
            foreach($data['val'] as $value) {
                $data['value'] = $value;
                $elements .= $this->_templates->format('input', [
                    'name' => $data['name'] . '[]',
                    'type' => 'hidden',
                    'templateVars' => $data['templateVars'],
                    'attrs' => $this->_templates->formatAttributes(
                        $data,
                        ['name', 'type', 'id', 'val', 'list', 'required']
                    )
                ]);
            }
        } else {
            $elements = $this->_templates->format('input', [
                'name' => $data['name'] . '[]',
                'type' => 'hidden',
                'attrs' => " value=\"{$this->emptyValue}\""
            ]);
        }

        // calculate existing ids
        $picky = "<div class='list'>";
        foreach($data['list'] as $value) {
            $class = false;
            if(in_array($value, $data['val'])) $class = 'active';
            if($value == $this->emptyValue) $class = 'empty';
            $picky.= "<pick". ($class ? " class=\"{$class}\"" : false) ."><div><span title='{$value}'>{$value}</span></div></pick>";
        }
        $picky .= "</div>";

        // return the actual template for this input type
        return "<div class='picky-widget'>" . $elements . $picky . '</div>';
    }
}
