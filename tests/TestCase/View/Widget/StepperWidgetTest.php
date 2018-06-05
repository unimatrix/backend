<?php

namespace Unimatrix\Backend\Test\TestCase\View\Widget;

use Cake\TestSuite\TestCase;
use Cake\View\View;
use Cake\View\StringTemplate;
use Unimatrix\Backend\View\Widget\StepperWidget;
use Unimatrix\Backend\View\Helper\BackendHelper;

class StepperWidgetTest extends TestCase
{
    protected $templates;
    protected $context;
    protected $data;

    public function setUp() {
        parent::setUp();
        $templates = [
            'input' => '<input type="{{type}}" name="{{name}}"{{attrs}}>',
        ];
        $this->templates = new StringTemplate($templates);
        $this->context = $this->getMockBuilder('Cake\View\Form\ContextInterface')->getMock();
		$view = new View(null);
		$helper = new BackendHelper($view);
		$this->data = [
		    'view' => $helper->getView(),
		    'name' => 'plus-minus'
		];
    }

    public function testWidgetHtmlDefault() {
        $text = new StepperWidget($this->templates);
        $result = $text->render($this->data, $this->context);

        $expected = [
            'div' => ['class' => 'stepper-widget'],
                ['span' => ['class' => 'button-stepper subtract']],
                    ['i' => ['class' => 'fa fa-minus', 'aria-hidden' => 'true']],
                    '/i',
                '/span',
                'input' => ['type' => 'text', 'name' => $this->data['name'], 'data-min' => 0, 'data-empty' => 'Please select a value', 'readonly' => 'readonly'],
                ['span' => ['class' => 'info-text']],
                '/span',
                ['span' => ['class' => 'button-stepper add']],
                    ['i' => ['class' => 'fa fa-plus', 'aria-hidden' => 'true']],
                    '/i',
                '/span',
            '/div'
        ];
        $this->assertHtml($expected, $result, true);
    }

    public function testWidgetHtmlFull() {
        $text = new StepperWidget($this->templates);
        $data = $this->data + [
            'min' => -5,
            'max' => 5,
            'skip' => 2,
            'empty' => '',
            'suffix' => '123|456'
        ];
        $result = $text->render($data, $this->context);

        $expected = [
            'div' => ['class' => 'stepper-widget'],
                ['span' => ['class' => 'button-stepper subtract']],
                    ['i' => ['class' => 'fa fa-minus', 'aria-hidden' => 'true']],
                    '/i',
                '/span',
                'input' => [
                    'type' => 'text',
                    'name' => $this->data['name'],
                    'data-min' => $data['min'],
                    'data-max' => $data['max'],
                    'data-skip' => $data['skip'],
                    'data-empty' => $data['empty'],
                    'data-suffix' => $data['suffix'],
                    'readonly' => 'readonly'
                ],
                ['span' => ['class' => 'info-text']],
                '/span',
                ['span' => ['class' => 'button-stepper add']],
                    ['i' => ['class' => 'fa fa-plus', 'aria-hidden' => 'true']],
                    '/i',
                '/span',
            '/div'
        ];
        $this->assertHtml($expected, $result);
    }
}
