<?php

namespace Unimatrix\Backend\Test\TestCase\View\Widget;

use Cake\TestSuite\TestCase;
use Cake\Core\Plugin;
use Cake\View\View;
use Cake\View\StringTemplate;
use Unimatrix\Backend\View\Widget\WysiwygWidget;
use Unimatrix\Backend\View\Helper\BackendHelper;

class WysiwygWidgetTest extends TestCase
{
    protected $templates;
    protected $context;
    protected $data;

    public function setUp() {
        parent::setUp();
        Plugin::load('Unimatrix/Backend', ['path' => PLUGIN_PATH . DS]);
        $templates = [
            'textarea' => '<textarea name="{{name}}"{{attrs}}>{{value}}</textarea>',
        ];
        $this->templates = new StringTemplate($templates);
        $this->context = $this->getMockBuilder('Cake\View\Form\ContextInterface')->getMock();
		$view = new View(null);
		$helper = new BackendHelper($view);
		$this->data = [
		    'view' => $helper->getView(),
		    'name' => 'body'
		];
    }

    public function tearDOwn() {
        parent::tearDown();
        Plugin::unload();
    }

    public function testWidgetHtmlNoValue() {
        $text = new WysiwygWidget($this->templates);
        $result = $text->render($this->data, $this->context);

        $expected = [
            'div' => ['class' => 'wysiwyg-widget'],
                'textarea' => ['name' => $this->data['name'], 'rows' => 5],
                '/textarea',
                ['script' => true],
                    'var CKEDITOR_BASEPATH=\'/unimatrix/backend/js/scripts/ckeditor/\'',
                '/script',
                ['script' => true],
                    'var CKFINDER_BASEPATH=\'/unimatrix/backend/js/scripts/ckfinder/\'',
                '/script',
            '/div'
        ];
        $this->assertHtml($expected, $result);
    }

    public function testWidgetHtmlWithValue() {
        $text = new WysiwygWidget($this->templates);
        $data = $this->data + [
            'val' => '<p>Paragraph</p>',
        ];
        $result = $text->render($data, $this->context);

        $expected = [
            'div' => ['class' => 'wysiwyg-widget'],
                'textarea' => ['name' => $this->data['name'], 'rows' => 5],
                    h($data['val']),
                '/textarea',
                ['script' => true],
                    'var CKEDITOR_BASEPATH=\'/unimatrix/backend/js/scripts/ckeditor/\'',
                '/script',
                ['script' => true],
                    'var CKFINDER_BASEPATH=\'/unimatrix/backend/js/scripts/ckfinder/\'',
                '/script',
            '/div'
        ];
        $this->assertHtml($expected, $result);
    }
}
