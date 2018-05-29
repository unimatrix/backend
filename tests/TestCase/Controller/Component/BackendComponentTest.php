<?php

namespace Unimatrix\Backend\Test\TestCase\Controller\Component;

use Cake\TestSuite\TestCase;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\I18n\FrozenTime;
use Cake\Http\ServerRequest;
use Cake\Database\ValueBinder;
use Cake\Database\Expression\QueryExpression;
use Cake\Controller\Controller;
use Cake\Controller\ComponentRegistry;
use Cake\Controller\Component\SecurityComponent;
use Unimatrix\Cake\Controller\Component\CookieComponent;
use Unimatrix\Backend\Controller\Component\BackendComponent;
use Unimatrix\Backend\Controller\Component\FlashComponent;
use Unimatrix\Backend\Controller\Component\AuthComponent;
use Unimatrix\Backend\Controller\Component\SearchLogic;
use Unimatrix\Backend\Form\Backend\SearchForm;

class BackendComponentTest extends TestCase
{
    public function testInitialize() {
        Configure::write('Backend.security.enabled', true);
        Configure::write('Backend.security.ssl', true);

        $controller = new Controller();
        $registry = new ComponentRegistry($controller);
        $component = new BackendComponent($registry);

        $this->assertInstanceOf(BackendComponent::class, $component);
        $this->assertInstanceOf(SecurityComponent::class, $component->getController()->Security);
        $this->assertInstanceOf(CookieComponent::class, $component->getController()->Cookie);
        $this->assertInstanceOf(FlashComponent::class, $component->getController()->Flash);
        $this->assertInstanceOf(AuthComponent::class, $component->getController()->Auth);

        $this->assertEquals(['*'], $component->getController()->Security->getConfig('requireSecure'));
    }

    public function testStartup() {
        $request = new ServerRequest([
            'post' => [
                '_calendar_input_date' => '12-12-2012',
                'tags' => ['_to_empty_array_']
            ],
            'environment' => [
                'REQUEST_METHOD' => 'POST'
            ]
        ]);
        $controller = new Controller($request);
        $registry = new ComponentRegistry($controller);
        $component = new BackendComponent($registry);
        $component->startup(new Event('Controller.startup'));

        $request = $component->getController()->getRequest();
        $this->assertEquals(new FrozenTime('12-12-2012'), $request->getData('date'));
        $this->assertEquals([], $request->getData('tags'));
    }

    public function testSearchPostWithValue() {
        $search = '#1';
        $request = new ServerRequest([
            'post' => [
                'search' => $search
            ],
            'environment' => [
                'REQUEST_METHOD' => 'POST'
            ]
        ]);
        $request = $request->withQueryParams([
            'page' => 100,
        ]);
        $controller = $this->getMockBuilder(Controller::class)
            ->setConstructorArgs([$request])
            ->setMethods(['redirect'])
            ->getMock();
        $registry = new ComponentRegistry($controller);
        $component = new BackendComponent($registry);
        $controller->expects($this->once())
            ->method('redirect')
            ->with($this->equalTo('/?search=' . urlencode($search)));

        $component->search('Articles', ['Articles.body']);
        $this->assertInstanceOf(SearchForm::class, $controller->viewVars['search']);
    }

    public function testSearchPostEmpty() {
        $request = new ServerRequest([
            'post' => [
                'search' => ''
            ],
            'environment' => [
                'REQUEST_METHOD' => 'POST'
            ]
        ]);
        $request = $request->withQueryParams([
            'page' => 100,
            'search' => 'old-search'
        ]);
        $controller = $this->getMockBuilder(Controller::class)
            ->setConstructorArgs([$request])
            ->setMethods(['redirect'])
            ->getMock();
        $registry = new ComponentRegistry($controller);
        $component = new BackendComponent($registry);
        $controller->expects($this->once())
            ->method('redirect')
            ->with($this->equalTo('/?page=100'));

        $component->search('Articles', ['Articles.body']);
        $this->assertEquals([], $controller->viewVars['highlight']);
    }

    public function testSearchComputeEmpty() {
        $search = new SearchLogic('Articles');
        $this->assertFalse($search->compute(' '));
    }

    public function testSearchById() {
        $request = new ServerRequest([
            'environment' => [
                'REQUEST_METHOD' => 'GET'
            ]
        ]);
        $request = $request->withQueryParams([
            'search' => '#1'
        ]);
        $controller = new Controller($request);
        $registry = new ComponentRegistry($controller);
        $component = new BackendComponent($registry);
        $conditions = $component->search('Articles', ['Articles.body']);

        $binder = new ValueBinder();
        $expression = new QueryExpression();
        $result = $conditions['OR']($expression, $expression);

        $this->assertSame('Articles.id = :c0', $result->sql($binder));
        $expected = [
            ':c0' => ['value' => '1', 'type' => null, 'placeholder' => 'c0'],
        ];
        $this->assertEquals($expected, $binder->bindings());
        $this->assertEquals(['id' => ['1']], $controller->viewVars['highlight']);
    }

    public function testSearchByText() {
        $request = new ServerRequest([
            'environment' => [
                'REQUEST_METHOD' => 'GET'
            ]
        ]);
        $request = $request->withQueryParams([
            'search' => 'whatever text here'
        ]);
        $controller = new Controller($request);
        $registry = new ComponentRegistry($controller);
        $component = new BackendComponent($registry);
        $conditions = $component->search('Articles', ['Articles.name', 'Articles.body']);

        $result = null;
        $binder = new ValueBinder();
        $expression = new QueryExpression();
        foreach($conditions['OR'] as $pass)
            $result = $pass($expression, $expression);

        $this->assertSame('(Articles.name LIKE :c0 AND Articles.body LIKE :c1)', $result->sql($binder));
        $expected = [
            ':c0' => ['value' => '%whatever text here%', 'type' => null, 'placeholder' => 'c0'],
            ':c1' => ['value' => '%whatever text here%', 'type' => null, 'placeholder' => 'c1'],
        ];
        $this->assertEquals($expected, $binder->bindings());
        $this->assertEquals([
            'name' => ['whatever text here'],
            'body' => ['whatever text here']
        ], $controller->viewVars['highlight']);
    }

    public function testSearchByFieldEquals() {
        $request = new ServerRequest([
            'environment' => [
                'REQUEST_METHOD' => 'GET'
            ]
        ]);
        $request = $request->withQueryParams([
            'search' => 'Articles.slug=:article-one'
        ]);
        $controller = new Controller($request);
        $registry = new ComponentRegistry($controller);
        $component = new BackendComponent($registry);
        $conditions = $component->search('Articles', ['Articles.name']);

        $binder = new ValueBinder();
        $expression = new QueryExpression();
        $result = $conditions['OR']($expression, $expression);

        $this->assertSame('Articles.slug = :c0', $result->sql($binder));
        $expected = [
            ':c0' => ['value' => 'article-one', 'type' => null, 'placeholder' => 'c0'],
        ];
        $this->assertEquals($expected, $binder->bindings());
        $this->assertEquals(['slug' => ['article-one']], $controller->viewVars['highlight']);
    }

    public function testSearchByFieldNotEquals() {
        $request = new ServerRequest([
            'environment' => [
                'REQUEST_METHOD' => 'GET'
            ]
        ]);
        $request = $request->withQueryParams([
            'search' => 'Articles.slug!=:article-two'
        ]);
        $controller = new Controller($request);
        $registry = new ComponentRegistry($controller);
        $component = new BackendComponent($registry);
        $conditions = $component->search('Articles', ['Articles.name']);

        $binder = new ValueBinder();
        $expression = new QueryExpression();
        $result = $conditions['OR']($expression, $expression);

        $this->assertSame('Articles.slug != :c0', $result->sql($binder));
        $expected = [
            ':c0' => ['value' => 'article-two', 'type' => null, 'placeholder' => 'c0'],
        ];
        $this->assertEquals($expected, $binder->bindings());
        $this->assertEquals(['slug' => ['article-two']], $controller->viewVars['highlight']);
    }

    public function testSearchByMultipleFields() {
        $request = new ServerRequest([
            'environment' => [
                'REQUEST_METHOD' => 'GET'
            ]
        ]);
        $request = $request->withQueryParams([
            'search' => 'Articles.id:5 && Articles.name!=:Tesla || date_format(date, \'%x%v\')=:201807'
        ]);
        $controller = new Controller($request);
        $registry = new ComponentRegistry($controller);
        $component = new BackendComponent($registry);
        $conditions = $component->search('Articles', ['Articles.name']);

        $result = null;
        $binder = new ValueBinder();
        $expression = new QueryExpression();
        foreach($conditions['OR'] as $pass)
            foreach($pass as $second)
                $result = $second['OR']($expression, $expression);

        $this->assertSame('(Articles.id = :c0 AND Articles.name != :c1 AND date_format(Articles.date, \'%x%v\') = :c2)', $result->sql($binder));
        $expected = [
            ':c0' => ['value' => '5', 'type' => null, 'placeholder' => 'c0'],
            ':c1' => ['value' => 'Tesla', 'type' => null, 'placeholder' => 'c1'],
            ':c2' => ['value' => '201807', 'type' => null, 'placeholder' => 'c2'],
        ];
        $this->assertEquals($expected, $binder->bindings());
        $this->assertEquals([
            'id' => ['5'],
            'name' => ['Tesla'],
            'date' => ['201807']
        ], $controller->viewVars['highlight']);
    }
}
