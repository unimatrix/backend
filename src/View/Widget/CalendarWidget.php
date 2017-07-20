<?php

namespace Unimatrix\Backend\View\Widget;

use Cake\View\View;
use Cake\View\Widget\BasicWidget;
use Cake\View\Form\ContextInterface;
use Cake\I18n\FrozenTime;
use DateTimeInterface;

/**
 * Calendar
 * This widget is used to display a calendar picker
 * @see http://www.pigno.se/barn/PIGNOSE-Calendar/
 *
 * Example:
 * ---------------------------------------------------------------------------------
 * echo $this->Form->control('date', ['type' => 'calendar']);
 *
 * @author Flavius
 * @version 1.0
 */
class CalendarWidget extends BasicWidget
{
    // extra file prerequisites
    private $prerequisites = [
        // styles
        'style' => [
            'Unimatrix/Backend.jquery/pignose.min.css',
            'Unimatrix/Backend.widgets/calendar.css',
        ],

        // javascript
        'script' => [
            'Unimatrix/Backend.scripts/moment.min.js',
            'Unimatrix/Backend.jquery/pignose.min.js',
            'Unimatrix/Backend.widgets/calendar.js',
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
            'readonly' => true,
            'templateVars' => []
        ];

        // require prerequisites
        $this->require($data['view']);
        unset($data['view']);

        // set value
        $data['value'] = $data['val'];
        unset($data['val']);

        // transform into frozen time (if not already)
        if(!$data['value'] instanceof DateTimeInterface)
            $data['value'] = new FrozenTime($data['value']);

        // set value
        $data['value'] = $data['value']->format('d-M-Y');

        // create input
        $input = $this->_templates->format('input', [
            'name' => '_calendar_input_' . $data['name'],
            'type' => 'text',
            'attrs' => $this->_templates->formatAttributes($data, ['type', 'name', 'mode']),
        ]);

        // return the actual template for this input type
        return "<div class='calendar-widget'>" . $input . "</div>";
    }

    /**
     * Security above all
     */
    public function secureFields(array $data) {
        $data['name'] = '_calendar_input_' . $data['name'];
        if (!isset($data['name']) || $data['name'] === '')
            return [];

        return [$data['name']];
    }
}
