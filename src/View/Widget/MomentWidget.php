<?php

namespace Unimatrix\Backend\View\Widget;

use Cake\View\View;
use Cake\View\Widget\BasicWidget;
use Cake\View\Form\ContextInterface;
use Cake\I18n\FrozenTime;
use DateTimeInterface;

/**
 * Moment
 * This widget is used in conjunction with the dtpicker jquery plugin
 * @see https://curioussolutions.github.io/DateTimePicker/
 *
 * Example:
 * ---------------------------------------------------------------------------------
 * echo $this->Form->control('date1', ['type' => 'moment']); // mode is `datetime` by default
 * echo $this->Form->control('date2', ['type' => 'moment', 'mode' => 'date']);
 * echo $this->Form->control('date3', ['type' => 'moment', 'mode' => 'time']);
 *
 * @author Flavius
 * @version 0.3
 */
class MomentWidget extends BasicWidget
{
    // extra file prerequisites
    private $prerequisites = [
        // styles
        'style' => [
            'Unimatrix/Backend.jquery/dtpicker.min.css',
            'Unimatrix/Backend.widgets/moment.css',
        ],

        // javascript
        'script' => [
            'Unimatrix/Backend.jquery/dtpicker.min.js',
            'Unimatrix/Backend.widgets/moment.js',
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
            'val' => null,
            'type' => 'text',
            'mode' => 'datetime',
            'escape' => true,
            'readonly' => true,
            'templateVars' => []
        ];

        // require prerequisites
        $this->require($data['view']);
        unset($data['view']);

        // mode value and class
        $mode = $data['data-field'] = $data['mode'];
        $hval = $data['value'] = $data['val'];
        $data['class'] = $data['type'];
        unset($data['val'], $data['mode']);

        // transform into frozen time (if not already)
        if(!$data['value'] instanceof DateTimeInterface)
            $data['value'] = new FrozenTime($data['value']);

        // transform values
        if($mode == 'datetime') {
            $hval = $data['value']->format('Y-m-d H:i:s');
            $data['value'] = $data['value']->format('d-M-Y H:i:s');
        }
        if($mode == 'date') {
            $hval = $data['value']->format('Y-m-d');
            $data['value'] = $data['value']->format('d-M-Y');
        }
        if($mode == 'time')
            $hval = $data['value'] = $data['value']->format('H:i:s');

        // create the hidden input
        $hidden = $this->_templates->format('input', [
            'name' => $data['name'],
            'type' => 'hidden',
            'attrs' => $this->_templates->formatAttributes(['value' => $hval]),
        ]);

        // create the fake input
        $fake = $this->_templates->format('input', [
            'name' => '_fake_input_' . $data['name'],
            'type' => 'text',
            'templateVars' => $data['templateVars'],
            'attrs' => $this->_templates->formatAttributes(
                $data,
                ['name', 'type']
            ),
        ]);

        // render
        return "<div class='moment-widget'>" . $hidden . $fake . "<div class='moment'></div></div>";
    }

    /**
     * Security above all
     */
    public function secureFields(array $data) {
        if(!isset($data['name']) || $data['name'] === '')
            return [];

        return [$data['name'], '_fake_input_' . $data['name']];
    }
}
