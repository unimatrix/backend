<?php

namespace Unimatrix\Backend\Test\TestCase\View\Helper;

use Cake\TestSuite\TestCase;
use Cake\Event\Event;
use Cake\ORM\Entity;
use Cake\View\View;
use Cake\View\Helper\TextHelper;
use Unimatrix\Cake\View\Helper\DebugHelper;
use Unimatrix\Cake\View\Helper\MinifyHelper;
use Unimatrix\Cake\View\Helper\FormHelper;
use Unimatrix\Backend\View\Helper\BackendHelper;

class BackendHelperTest extends TestCase
{
    protected $helper;
    protected $entity;

	public function setUp() {
		parent::setUp();
		$view = new View(null);
		$this->helper = new BackendHelper($view);
        $this->entity = new Entity();
        $this->entity->set('field', 'value');
	}

    public function testAdditionalHelpersLoaded() {
        $this->assertInstanceOf(TextHelper::class, $this->helper->Text);
    }

    public function testRequiredHelpersLoaded() {
        $view = $this->helper->getView();

        $this->assertInstanceOf(DebugHelper::class, $view->Debug);
        $this->assertInstanceOf(MinifyHelper::class, $view->Minify);
        $this->assertInstanceOf(FormHelper::class, $view->Form);
    }

    public function testSettingLayout() {
        $this->helper->beforeRender(new Event('test-event'), 'test-file.ctp');
        $this->assertSame('Unimatrix/Backend.backend', $this->helper->getView()->getLayout());
    }

    public function testSearchWithoutHighlight() {
        $search = $this->helper->search($this->entity, 'field');
        $this->assertSame('value', $search);
    }

    public function testSearchWithHighlight() {
        $view = $this->helper->getView();
        $view->set('highlight', ['field' => ['value']]);
        $search = $this->helper->search($this->entity, 'field');

        $expected = [
            ['span' => ['class' => 'highlight']],
            'value',
            '/span'
        ];
        $this->assertHtml($expected, $search);
    }

    public function testSearchNotSearchedButTruncated() {
        $search = $this->helper->search($this->entity, 'field', ['truncate' => 2]);
        $this->assertSame('v…', $search);
    }
}
