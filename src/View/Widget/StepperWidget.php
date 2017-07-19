<?php

namespace Unimatrix\Backend\View\Widget;

use Cake\View\View;
use Cake\View\Widget\BasicWidget;
use Cake\View\Form\ContextInterface;

/**
 * Stepper
 * This widget adds plus and minus (add / subtract) buttons to a integer input
 *
 * Example:
 * ---------------------------------------------------------------------------------
 * echo $this->Form->control('date3', ['type' => 'stepper', 'min' => '1', 'max' => '10', 'empty' => 'Please select a value', 'suffix' => 'room|rooms']);
 *
 * @author Flavius
 * @version 1.0
 */
class StepperWidget extends BasicWidget
{
    // extra file prerequisites
    private $prerequisites = [
        // styles
        'style' => [
            'Unimatrix/Backend.widgets/stepper.css',
        ],

        // javascript
        'script' => [
            'Unimatrix/Backend.widgets/stepper.js',
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
            'name' => '',
            'readonly' => true,
            'templateVars' => []
        ];

        // require prerequisites
        $this->require($data['view']);
        unset($data['view']);

        // set value
        $data['value'] = $data['val'];
        unset($data['val']);

        // do data attributes
        $attrs = $this->_templates->formatAttributes($data, ['name', 'type', 'readonly', 'value', 'id', 'required']);
        $dataAttrs = ' data-' . trim(str_replace('" ', '" data-', $attrs));

        // create input
        $input = $this->_templates->format('input', [
            'name' => $data['name'],
            'type' => 'text',
            'templateVars' => $data['templateVars'],
            'attrs' => $dataAttrs . $this->_templates->formatAttributes(
                $data,
                ['name', 'type', 'min', 'max', 'empty', 'suffix']
            ),
        ]);

        // render
        return "<div class='stepper-widget'>" .
            "<span class='button-stepper subtract'><i class='fa fa-minus' aria-hidden='true'></i></span>" .
            $input . "<span class='info-text'></span>" .
            "<span class='button-stepper add'><i class='fa fa-plus' aria-hidden='true'></i></span>" .
        "</div>";
    }
}
