<?php

namespace Unimatrix\Backend\Form\Backend;

use Cake\Form\Form;
use Cake\Form\Schema;
use Cake\Validation\Validator;

/**
 * Backend Search Form
 * The search form used for every table out there
 *
 * @author Flavius
 * @version 1.0
 */
class SearchForm extends Form
{
    /**
     * (non-PHPdoc)
     * @see \Cake\Form\Form::_buildSchema()
     */
    protected function _buildSchema(Schema $schema) {
        return $schema->addField('search', ['type' => 'string']);
    }

    /**
     * Validation rules
     * @param Validator $validator
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator) {
        $validator->allowEmpty('search')->add('search', 'length', [
            'rule' => ['minLength', 2],
            'message' => __d('Unimatrix/backend', 'Search query is too short')
        ]);

        return $validator;
    }
}
