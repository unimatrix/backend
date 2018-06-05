<?php

namespace Unimatrix\Backend\Test\TestCase\View\Widget;

use Cake\TestSuite\TestCase;
use Cake\View\View;
use Cake\I18n\FrozenTime;
use Cake\View\StringTemplate;
use Unimatrix\Backend\View\Widget\CalendarWidget;
use Unimatrix\Backend\View\Helper\BackendHelper;

class CalendarWidgetTest extends TestCase
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
		    'name' => 'created'
		];
    }

    public function testWidgetHtmlNoValue() {
        $text = new CalendarWidget($this->templates);
        $result = $text->render($this->data, $this->context);

        $expected = [
            'div' => ['class' => 'calendar-widget'],
                'input' => [
                    'type' => 'text',
                    'name' => '_calendar_input_created',
                    'readonly' => 'readonly'
                ],
            '/div'
        ];
        $this->assertHtml($expected, $result);
    }

    public function testWidgetHtmlWithObjectValue() {
        $text = new CalendarWidget($this->templates);
        $data = $this->data + [
            'val' => new FrozenTime('12-12-2017')
        ];
        $result = $text->render($data, $this->context);

        $expected = [
            'div' => ['class' => 'calendar-widget'],
                'input' => [
                    'type' => 'text',
                    'name' => '_calendar_input_created',
                    'readonly' => 'readonly',
                    'value' => $data['val']->format('d-M-Y')
                ],
            '/div'
        ];
        $this->assertHtml($expected, $result);
    }

    public function testWidgetHtmlWithStringValue() {
        $text = new CalendarWidget($this->templates);
        $data = $this->data + [
            'val' => '12-12-2017'
        ];
        $result = $text->render($data, $this->context);

        $expected = [
            'div' => ['class' => 'calendar-widget'],
                'input' => [
                    'type' => 'text',
                    'name' => '_calendar_input_created',
                    'readonly' => 'readonly',
                    'value' => (new FrozenTime($data['val']))->format('d-M-Y')
                ],
            '/div'
        ];
        $this->assertHtml($expected, $result);
    }

    public function testSecureFields() {
        $input = new CalendarWidget($this->templates);

        $this->assertSame(['_calendar_input_'], $input->secureFields([]));
        $this->assertSame(['_calendar_input_created'], $input->secureFields(['name' => 'created']));
    }
}
