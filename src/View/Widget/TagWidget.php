<?php

namespace Unimatrix\Backend\View\Widget;

use Cake\View\View;
use Cake\View\Widget\BasicWidget;
use Cake\View\Form\ContextInterface;

/**
 * Tag
 * This widget is used to select a tag from the provided list
 *
 * Example:
 * ---------------------------------------------------------------------------------
 * echo $this->Form->control('tags', ['type' => 'tag', 'list' => 'One Tag, Another Tag', 'placeholder' => 'Add tag...'])
 *
 * @author Flavius
 * @version 0.1
 */
class TagWidget extends BasicWidget
{
    // extra file prerequisites
    private $prerequisites = [
        // css
        'style' => [
            'Unimatrix/Backend.scripts/tagify.css',
            'Unimatrix/Backend.widgets/tag.css',
        ],

        // javascript
        'script' => [
            'Unimatrix/Backend.scripts/tagify.min.js',
            'Unimatrix/Backend.widgets/tag.js',
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
            'val' => '',
            'name' => '',
            'escape' => true,
            'rows' => 5,
            'templateVars' => []
        ];

        // require prerequisites
        $this->require($data['view']);
        unset($data['view']);

        // calculate existing tags
        $tags = null;
        if(isset($data['list'])) {
            $tags = "<div class='list'><label>Or select some from this list:</label>";
            $existing = explode(',', str_replace(', ', ',', $data['val']));
            foreach(explode(',', str_replace(', ', ',', $data['list'])) as $value)
                $tags .= "<tag". (in_array($value, $existing) ? ' class="hidden"' : false) ."><div><span title='{$value}'>{$value}</span></div></tag>";
            $tags .= "</div>";
        }

        // create the textarea
        $textarea = $this->_templates->format('textarea', [
            'name' => $data['name'],
            'value' => $data['escape'] ? h($data['val']) : $data['val'],
            'templateVars' => $data['templateVars'],
            'attrs' => $this->_templates->formatAttributes(
                $data,
                ['name', 'val', 'type', 'list', 'required']
            )
        ]);

        // return the actual template for this input type
        return "<div class='tag-widget'>" . $textarea . $tags . '</div>';
    }
}
