<?php

namespace Unimatrix\Backend\Test\TestCase\Form\Backend;

use Cake\TestSuite\TestCase;
use Unimatrix\Backend\Form\Backend\SearchForm;

class SearchFormTest extends TestCase
{
    public function testFormSchemaAndValidation() {
        $form = new SearchForm();

        $this->assertSame([
            'type' => 'string',
            'length' => null,
            'precision' => null,
            'default' => null
        ], $form->schema()->field('search'));
        $this->assertSame(['search' => ['length' => 'Search query is too short']], $form->getValidator()->errors(['search' => '1']));
        $this->assertSame([], $form->getValidator()->errors(['search' => '12']));
    }
}
