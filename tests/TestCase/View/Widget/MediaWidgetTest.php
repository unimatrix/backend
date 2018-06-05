<?php

namespace Unimatrix\Backend\Test\TestCase\View\Widget;

use Cake\TestSuite\TestCase;
use Cake\Core\Plugin;
use Cake\Routing\Router;
use Cake\View\View;
use Cake\View\StringTemplate;
use Unimatrix\Backend\View\Widget\MediaWidget;
use Unimatrix\Backend\View\Helper\BackendHelper;
use RuntimeException;

class MediaWidgetTest extends TestCase
{
    protected $templates;
    protected $context;
    protected $data;

    protected $uuidRegex = 'preg:/([a-f0-9]{8}-[a-f0-9]{4}-4[a-f0-9]{3}-[89aAbB][a-f0-9]{3}-[a-f0-9]{12})/';

    public function setUp() {
        parent::setUp();
        Router::connect('/');
        Plugin::load('Unimatrix/Backend', ['path' => PLUGIN_PATH . DS]);
        $templates = [
            'input' => '<input type="{{type}}" name="{{name}}"{{attrs}}>',
        ];
        $this->templates = new StringTemplate($templates);
        $this->context = $this->getMockBuilder('Cake\View\Form\ContextInterface')->getMock();
		$view = new View(null);
		$helper = new BackendHelper($view);
		$this->data = [
		    'view' => $helper->getView(),
		    'name' => 'media'
		];
    }

    public function tearDOwn() {
        parent::tearDown();
        Router::reload();
        Plugin::unload();
    }

    public function testWidgetHtmlSingleNoValue() {
        $text = new MediaWidget($this->templates);
        $result = $text->render($this->data, $this->context);

        $expected = [
            ['div' => ['class' => 'media-widget']],
                ['div' => ['class' => 'list']],
                    'media' => ['id' => $this->uuidRegex, 'class' => 'new'],
                        'img' => ['src' => '/unimatrix/backend/img/widgets/media-plus.png', 'alt' => ''],
                        'a' => ['href' => '#', 'target' => '_blank'],
                            ['i' => ['class' => 'fa fa-eye', 'aria-hidden' => 'true']],
                            '/i',
                            'Full Image',
                        '/a',
                        ['i' => ['class' => 'fa fa-times', 'aria-hidden' => 'true']],
                        '/i',
                    '/media',
                '/div',
                'input' => ['type' => 'hidden', 'name' => 'media'],
                '<script',
                    'var CKFINDER_BASEPATH=\'/unimatrix/backend/js/scripts/ckfinder/\'',
                '/script',
            '/div'
        ];
        $this->assertHtml($expected, $result);
    }

    public function testWidgetHtmlSingleWithValue() {
        $text = new MediaWidget($this->templates);
        $data = $this->data + [
            'val' => '/up/images/sample-image.jpg'
        ];
        $result = $text->render($data, $this->context);

        $expected = [
            ['div' => ['class' => 'media-widget']],
                ['div' => ['class' => 'list']],
                    'media' => ['id' => $this->uuidRegex],
                        'img' => ['src' => $data['val'], 'alt' => ''],
                        'a' => ['href' => $data['val'], 'target' => '_blank'],
                            ['i' => ['class' => 'fa fa-eye', 'aria-hidden' => 'true']],
                            '/i',
                            'Full Image',
                        '/a',
                        ['i' => ['class' => 'fa fa-times', 'aria-hidden' => 'true']],
                        '/i',
                    '/media',
                '/div',
                'input' => ['type' => 'hidden', 'name' => 'media', 'value' => $data['val']],
                '<script',
                    'var CKFINDER_BASEPATH=\'/unimatrix/backend/js/scripts/ckfinder/\'',
                '/script',
            '/div'
        ];
        $this->assertHtml($expected, $result, true);
    }

    public function testWidgetHtmlMultipleNoValue() {
        $text = new MediaWidget($this->templates);
        $data = $this->data + [
            'multiple' => true
        ];
        $result = $text->render($data, $this->context);

        $expected = [
            ['div' => ['class' => 'media-widget multiple']],
                ['div' => ['class' => 'list']],
                    'media' => ['id' => $this->uuidRegex, 'class' => 'new'],
                        'img' => ['src' => '/unimatrix/backend/img/widgets/media-plus.png', 'alt' => ''],
                        'a' => ['href' => '#', 'target' => '_blank'],
                            ['i' => ['class' => 'fa fa-eye', 'aria-hidden' => 'true']],
                            '/i',
                            'Full Image',
                        '/a',
                        ['i' => ['class' => 'fa fa-times', 'aria-hidden' => 'true']],
                        '/i',
                    '/media',
                '/div',
                'input' => ['type' => 'hidden', 'name' => 'media[]', 'value' => '_to_empty_array_'],
                '<script',
                    'var CKFINDER_BASEPATH=\'/unimatrix/backend/js/scripts/ckfinder/\'',
                '/script',
            '/div'
        ];
        $this->assertHtml($expected, $result);
    }

    public function testWidgetHtmlMultipleWithInvalidValue() {
        $text = new MediaWidget($this->templates);
        $data = $this->data + [
            'multiple' => true,
            'val' => '12345'
        ];

        $this->expectException(RuntimeException::class);
        $result = $text->render($data, $this->context);
    }

    public function testWidgetHtmlMultipleWithOneValue() {
        $text = new MediaWidget($this->templates);
        $data = $this->data + [
            'multiple' => true,
            'val' => [
                '/up/images/sample-image.jpg'
            ]
        ];
        $result = $text->render($data, $this->context);

        $expected = [
            ['div' => ['class' => 'media-widget multiple']],
                ['div' => ['class' => 'list']],
                    ['media' => ['id' => $this->uuidRegex]],
                        ['img' => ['src' => $data['val'][0], 'alt' => '']],
                        ['a' => ['href' => $data['val'][0], 'target' => '_blank']],
                            ['i' => ['class' => 'fa fa-eye', 'aria-hidden' => 'true']],
                            '/i',
                            'Full Image',
                        '/a',
                        ['i' => ['class' => 'fa fa-times', 'aria-hidden' => 'true']],
                        '/i',
                    '/media',
                    ['media' => ['id' => $this->uuidRegex, 'class' => 'new']],
                        ['img' => ['src' => '/unimatrix/backend/img/widgets/media-plus.png', 'alt' => '']],
                        ['a' => ['href' => '#', 'target' => '_blank']],
                            ['i' => ['class' => 'fa fa-eye', 'aria-hidden' => 'true']],
                            '/i',
                            'Full Image',
                        '/a',
                        ['i' => ['class' => 'fa fa-times', 'aria-hidden' => 'true']],
                        '/i',
                    '/media',
                '/div',
                'input' => ['type' => 'hidden', 'name' => 'media[]', 'value' => $data['val'][0]],
                '<script',
                    'var CKFINDER_BASEPATH=\'/unimatrix/backend/js/scripts/ckfinder/\'',
                '/script',
            '/div'
        ];
        $this->assertHtml($expected, $result);
    }

    public function testWidgetHtmlMultipleWithTwoValue() {
        $text = new MediaWidget($this->templates);
        $data = $this->data + [
            'multiple' => true,
            'val' => [
                '/up/images/sample-image.jpg',
                '/up/images/sample-image-2.jpg',
            ]
        ];
        $result = $text->render($data, $this->context);

        $expected = [
            ['div' => ['class' => 'media-widget multiple']],
                ['div' => ['class' => 'list']],
                    ['media' => ['id' => $this->uuidRegex]],
                        ['img' => ['src' => $data['val'][0], 'alt' => '']],
                        ['a' => ['href' => $data['val'][0], 'target' => '_blank']],
                            ['i' => ['class' => 'fa fa-eye', 'aria-hidden' => 'true']],
                            '/i',
                            'Full Image',
                        '/a',
                        ['i' => ['class' => 'fa fa-times', 'aria-hidden' => 'true']],
                        '/i',
                    '/media',
                    ['media' => ['id' => $this->uuidRegex]],
                        ['img' => ['src' => $data['val'][1], 'alt' => '']],
                        ['a' => ['href' => $data['val'][1], 'target' => '_blank']],
                            ['i' => ['class' => 'fa fa-eye', 'aria-hidden' => 'true']],
                            '/i',
                            'Full Image',
                        '/a',
                        ['i' => ['class' => 'fa fa-times', 'aria-hidden' => 'true']],
                        '/i',
                    '/media',
                    ['media' => ['id' => $this->uuidRegex, 'class' => 'new']],
                        ['img' => ['src' => '/unimatrix/backend/img/widgets/media-plus.png', 'alt' => '']],
                        ['a' => ['href' => '#', 'target' => '_blank']],
                            ['i' => ['class' => 'fa fa-eye', 'aria-hidden' => 'true']],
                            '/i',
                            'Full Image',
                        '/a',
                        ['i' => ['class' => 'fa fa-times', 'aria-hidden' => 'true']],
                        '/i',
                    '/media',
                '/div',
                ['input' => ['type' => 'hidden', 'name' => 'media[]', 'value' => $data['val'][0]]],
                ['input' => ['type' => 'hidden', 'name' => 'media[]', 'value' => $data['val'][1]]],
                '<script',
                    'var CKFINDER_BASEPATH=\'/unimatrix/backend/js/scripts/ckfinder/\'',
                '/script',
            '/div'
        ];
        $this->assertHtml($expected, $result);
    }
}
