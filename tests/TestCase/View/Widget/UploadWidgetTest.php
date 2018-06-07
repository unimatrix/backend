<?php

namespace Unimatrix\Backend\Test\TestCase\View\Widget;

use Cake\TestSuite\TestCase;
use Cake\Routing\Router;
use Cake\View\View;
use Cake\View\StringTemplate;
use Unimatrix\Backend\View\Widget\UploadWidget;
use Unimatrix\Backend\View\Helper\BackendHelper;

class UploadWidgetTest extends TestCase
{
    protected $templates;
    protected $context;
    protected $data;

    public function setUp() {
        parent::setUp();
        Router::connect('/');
        $templates = [
            'file' => '<input type="file" name="{{name}}"{{attrs}}>',
        ];
        $this->templates = new StringTemplate($templates);
        $this->context = $this->getMockBuilder('Cake\View\Form\ContextInterface')->getMock();
		$view = new View(null);
		$helper = new BackendHelper($view);
		$this->data = [
		    'view' => $helper->getView(),
		    'name' => 'picture'
		];
    }

    public function tearDOwn() {
        parent::tearDown();
        Router::reload();
    }

    public function testWidgetHtmlNoValue() {
        $text = new UploadWidget($this->templates);
        $result = $text->render($this->data, $this->context);

        $expected = [
            'div' => ['class' => 'upload-widget'],
                'input' => ['type' => 'file', 'name' => $this->data['name']],
            '/div'
        ];
        $this->assertHtml($expected, $result);
    }

    public function testWidgetHtmlWithValue() {
        $text = new UploadWidget($this->templates);
        $data = $this->data + [
            'val' => '/img/model/55350294-d948-4e36-96cc-16d79a18d403.jpg'
        ];
        $result = $text->render($data, $this->context);

        $expected = [
            ['div' => ['class' => 'upload-widget']],
                ['div' => ['class' => 'preview']],
                    'a' => ['href' => $data['val'], 'target' => '_blank'],
                        'img' => ['src' => $data['val'], 'alt' => ''],
                    '/a',
                '/div',
                'input' => ['type' => 'file', 'name' => $this->data['name']],
            '/div'
        ];
        $this->assertHtml($expected, $result);
    }
}
