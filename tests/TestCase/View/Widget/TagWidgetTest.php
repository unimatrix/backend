<?php

namespace Unimatrix\Backend\Test\TestCase\View\Widget;

use Cake\TestSuite\TestCase;
use Cake\View\View;
use Cake\View\StringTemplate;
use Unimatrix\Backend\View\Widget\TagWidget;
use Unimatrix\Backend\View\Helper\BackendHelper;

class TagWidgetTest extends TestCase
{
    protected $templates;
    protected $context;
    protected $data;

    public function setUp() {
        parent::setUp();
        $templates = [
            'textarea' => '<textarea name="{{name}}"{{attrs}}>{{value}}</textarea>',
        ];
        $this->templates = new StringTemplate($templates);
        $this->context = $this->getMockBuilder('Cake\View\Form\ContextInterface')->getMock();
		$view = new View(null);
		$helper = new BackendHelper($view);
		$this->data = [
		    'view' => $helper->getView(),
		    'name' => 'todo'
		];
    }

    public function testWidgetHtmlWithoutList() {
        $text = new TagWidget($this->templates);
        $result = $text->render($this->data, $this->context);

        $expected = [
            'div' => ['class' => 'tag-widget'],
                'textarea' => ['name' => $this->data['name'], 'rows' => 5],
                '/textarea',
            '/div'
        ];
        $this->assertHtml($expected, $result);
    }

    public function testWidgetHtmlWithList() {
        $text = new TagWidget($this->templates);
        $data = $this->data + [
            'list' => 'shopping, cleaning, relaxing'
        ];
        $result = $text->render($data, $this->context);

        $item = explode(', ', $data['list']);
        $expected = [
            ['div' => ['class' => 'tag-widget']],
                'textarea' => ['name' => $this->data['name'], 'rows' => 5],
                '/textarea',
                ['div' => ['class' => 'list']],
                    '<label',
                        'Or select some from this list:',
                    '/label',
                    ['tag' => true],
                        ['div' => true],
                            ['span' => ['title' => $item[0]]],
                                $item[0],
                            '/span',
                        '/div',
                    '/tag',
                    ['tag' => true],
                        ['div' => true],
                            ['span' => ['title' => $item[1]]],
                                $item[1],
                            '/span',
                        '/div',
                    '/tag',
                    ['tag' => true],
                        ['div' => true],
                            ['span' => ['title' => $item[2]]],
                                $item[2],
                            '/span',
                        '/div',
                    '/tag',
                '/div',
            '/div'
        ];
        $this->assertHtml($expected, $result);
    }

    public function testWidgetHtmlWithPlaceholder() {
        $text = new TagWidget($this->templates);
        $data = $this->data + [
            'placeholder' => 'continue?'
        ];
        $result = $text->render($data, $this->context);

        $expected = [
            ['div' => ['class' => 'tag-widget']],
                'textarea' => ['name' => $this->data['name'], 'placeholder' => $data['placeholder'], 'rows' => 5],
                '/textarea',
            '/div'
        ];
        $this->assertHtml($expected, $result);
    }
}
