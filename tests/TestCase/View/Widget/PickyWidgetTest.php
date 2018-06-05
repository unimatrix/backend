<?php

namespace Unimatrix\Backend\Test\TestCase\View\Widget;

use Cake\TestSuite\TestCase;
use Cake\View\View;
use Cake\View\StringTemplate;
use Unimatrix\Backend\View\Widget\PickyWidget;
use Unimatrix\Backend\View\Helper\BackendHelper;
use RuntimeException;

class PickyWidgetTest extends TestCase
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
		    'name' => 'selector'
		];
    }

    public function testWidgetHtmlWithNoList() {
        $text = new PickyWidget($this->templates);

        $this->expectException(RuntimeException::class);
        $result = $text->render($this->data, $this->context);
    }

    public function testWidgetHtmlWithInvalidList() {
        $text = new PickyWidget($this->templates);
        $data = $this->data + [
            'list' => 'test-list'
        ];

        $this->expectException(RuntimeException::class);
        $result = $text->render($data, $this->context);
    }

    public function testWidgetHtmlWithOneListAndNoValue() {
        $text = new PickyWidget($this->templates);
        $data = $this->data + [
            'list' => ['test-list']
        ];
        $result = $text->render($data, $this->context);

        $expected = [
            ['div' => ['class' => 'picky-widget']],
                'input' => ['type' => 'hidden', 'name' => $this->data['name'] . '[]', 'value' => '_to_empty_array_'],
                ['div' => ['class' => 'list']],
                    '<pick',
                        ['div' => true],
                            'span' => ['title' => $data['list'][0]],
                            $data['list'][0],
                            '/span',
                        '/div',
                    '/pick',
                '/div',
            '/div'
        ];
        $this->assertHtml($expected, $result);
    }

    public function testWidgetHtmlWithOneListAndInvalidValue() {
        $text = new PickyWidget($this->templates);
        $data = $this->data + [
            'list' => ['test-list'],
            'val' => 'some-value'
        ];

        $this->expectException(RuntimeException::class);
        $result = $text->render($data, $this->context);
    }

    public function testWidgetHtmlOneListAndWrongValue() {
        $text = new PickyWidget($this->templates);
        $data = $this->data + [
            'list' => ['test-list'],
            'val' => ['some-value']
        ];

        $this->expectException(RuntimeException::class);
        $result = $text->render($data, $this->context);
    }

    public function testWidgetHtmlWithOneListAndOneValue() {
        $text = new PickyWidget($this->templates);
        $data = $this->data + [
            'list' => ['test-list'],
            'val' => ['test-list']
        ];
        $result = $text->render($data, $this->context);

        $expected = [
            ['div' => ['class' => 'picky-widget']],
                'input' => ['type' => 'hidden', 'name' => $this->data['name'] . '[]', 'value' => $data['val'][0]],
                ['div' => ['class' => 'list']],
                    'pick' => ['class' => 'active'],
                        ['div' => true],
                            'span' => ['title' => $data['list'][0]],
                            $data['list'][0],
                            '/span',
                        '/div',
                    '/pick',
                '/div',
            '/div'
        ];
        $this->assertHtml($expected, $result);
    }

    public function testWidgetHtmlWithTwoListsAndOneValue() {
        $text = new PickyWidget($this->templates);
        $data = $this->data + [
            'list' => ['1st-value', '2nd-value'],
            'val' => ['1st-value']
        ];
        $result = $text->render($data, $this->context);

        $expected = [
            ['div' => ['class' => 'picky-widget']],
                'input' => ['type' => 'hidden', 'name' => $this->data['name'] . '[]', 'value' => $data['val'][0]],
                ['div' => ['class' => 'list']],
                    ['pick' => ['class' => 'active']],
                        ['div' => true],
                            ['span' => ['title' => $data['list'][0]]],
                            $data['list'][0],
                            '/span',
                        '/div',
                    '/pick',
                    ['pick' => true],
                        ['div' => true],
                            ['span' => ['title' => $data['list'][1]]],
                            $data['list'][1],
                            '/span',
                        '/div',
                    '/pick',
                '/div',
            '/div'
        ];
        $this->assertHtml($expected, $result);
    }

    public function testWidgetHtmlWithTwoListsAndTwoValues() {
        $text = new PickyWidget($this->templates);
        $data = $this->data + [
            'list' => ['1st-value', '2nd-value'],
            'val' => ['1st-value', '2nd-value']
        ];
        $result = $text->render($data, $this->context);

        $expected = [
            ['div' => ['class' => 'picky-widget']],
                ['input' => ['type' => 'hidden', 'name' => $this->data['name'] . '[]', 'value' => $data['val'][0]]],
                ['input' => ['type' => 'hidden', 'name' => $this->data['name'] . '[]', 'value' => $data['val'][1]]],
                ['div' => ['class' => 'list']],
                    ['pick' => ['class' => 'active']],
                        ['div' => true],
                            ['span' => ['title' => $data['list'][0]]],
                            $data['list'][0],
                            '/span',
                        '/div',
                    '/pick',
                    ['pick' => ['class' => 'active']],
                        ['div' => true],
                            ['span' => ['title' => $data['list'][1]]],
                            $data['list'][1],
                            '/span',
                        '/div',
                    '/pick',
                '/div',
            '/div'
        ];
        $this->assertHtml($expected, $result);
    }
}
