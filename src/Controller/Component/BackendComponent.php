<?php

namespace Unimatrix\Backend\Controller\Component;

use Cake\Event\Event;
use Cake\Core\Configure;
use Cake\I18n\FrozenTime;
use Cake\Controller\Component;
use Unimatrix\Backend\Form\Backend\SearchForm;

/**
 * Backend Component
 * This component loads all other necesary stuff for the backend,
 * it also handles some custom backend logic and request filtering
 *
 * @author Flavius
 * @version 1.4
 */
class BackendComponent extends Component
{
    // default config
    protected $_defaultConfig = [
        'Auth' => [
            'authenticate' => ['Unimatrix/Backend.Backend'],
            'loginAction' => [
                'controller' => 'Login',
                'action' => 'index',
                'plugin' => 'Unimatrix/Backend'
            ],
            'storage' => [
                'className' => 'Session',
                'key' => 'Auth.Backend'
            ]
        ]
    ];

    /**
     * {@inheritDoc}
     * @see \Cake\Controller\Component::initialize()
     */
    public function initialize(array $config) {
        parent::initialize($config);

        // we need these
        $controller = $this->getController();
        $request = $controller->request;

        // load security
        if(Configure::read('Backend.security.enabled')) {
            $controller->loadComponent('Security');
            if(Configure::read('Backend.security.ssl'))
                $controller->Security->requireSecure();
        }

        // load required components
        $controller->loadComponent('Unimatrix/Cake.Cookie', [
            'path' => $request->getAttribute('webroot') . $request->getParam('prefix'),
            'secure' => env('HTTPS'),
            'httpOnly' => Configure::read('Backend.security.enabled') ?: false
        ]);
        $controller->loadComponent('Unimatrix/Backend.Flash');
        $controller->loadComponent('Unimatrix/Backend.Auth', $this->_config['Auth']);
    }

    /**
     * Component startup
     * @param Event $event
     */
    public function startup(Event $event) {
        // fix incomming data from widgets
        $request = $this->getController()->request;
        if($request->is('post')) {
            $body = $request->getParsedBody();
            if(is_array($body)) {
                $dirty = false;
                foreach($body as $name => $value) {
                    // calendar widget
                    if(substr($name, 0, 16) === '_calendar_input_') {
                        $body[substr($name, 16)] = new FrozenTime($body[$name]);
                        unset($body[$name]);
                        $dirty = true;
                    }

                    // picky & media widget
                    if(is_array($value) && in_array('_to_empty_array_', $value, true)) {
                        $body[$name] = [];
                        $dirty = true;
                    }
                }

                // request changed, overwrite request
                if($dirty)
                    $this->getController()->request = $request->withParsedBody($body);
            }
        }
    }

    // search to view variable
    public $highlight = [];

    /**
     * Handle search for actions
     * @param string $alias The table alias
     * @param array $fields The like fields in case of text only search
     * @return array
     */
    public function search($alias, $fields = []) {
        // start
        $conditions = [];
        $form = new SearchForm();
        $ctrl = $this->getController();
        $search = new SearchLogic($alias, $fields);

        // on post
        $request = $ctrl->request;
        if($request->is('post')) {
            // mix and match query params with post search data
            $params = $request->getQueryParams();
            if(!$request->getData('search')) {
                if(isset($params['search']))
                    unset($params['search']);
            } else {
                $params['search'] = $request->getData('search');
                if(isset($params['page']))
                    unset($params['page']);
            }

            // build new uri
            $uri = $request->getUri()->withQuery(http_build_query($params));
            $target = $uri->getPath();
            if($uri->getQuery())
                $target .= '?' . $uri->getQuery();

            // redirect with brand new GET params
            $ctrl->redirect($target);

        // on get
        } else {
            // get term
            $term = $request->getQuery('search');
            if($term) {
                // fill value and execute form
                $ctrl->request = $request->withData('search', $term);
                if($form->execute($ctrl->request->getData())) {
                    // do conditions
                    if(strpos($term, '||') !== false || strpos($term, '&&') !== false) {
                        foreach(explode('||', $term) as $one) {
                            if(strpos($one, '&&') !== false) {
                                foreach(explode('&&', $one) as $two)
                                    $conditions['OR']['AND'][] = $search->compute($two);
                            } else $conditions['OR']['OR'][] = $search->compute($one);
                        }
                    } else $conditions = $search->compute($term);
                }
            }
        }

        // add to global highlight
        $this->highlight += $search->highlight;

        // send to template
        $ctrl->set('search', $form);
        $ctrl->set('highlight', $this->highlight);

        // return computed conditions
        return $conditions;
    }
}

/**
 * Search logic class
 *
 * @author Flavius
 * @version 1.1
 */
class SearchLogic
{
    // variables
    private $alias = null;
    private $fields = [];
    public $highlight = [];

    /**
     * Constructor
     * @param string $alias
     * @param array $fields
     */
    public function __construct($alias, $fields = []) {
        $this->alias = $alias;
        $this->fields = $fields;
    }

    /**
     * Compute conditions
     * @param string $term The search term
     * @return array of conditions with the `OR` key
     */
    public function compute($term) {
        // handle term
        $term = trim($term);
        if(!$term)
            return false;

        // start empty
        $conditions = [];

        // search by id (..#15..)
        if($term[0] == '#') {
            $field = 'id';
            $value = ltrim($term, '#');
            $this->buildHighlight($field, $value);
            $conditions = function($exp, $q) use($field, $value) {
                return $exp->eq("{$this->alias}.{$field}", $value);
            };

        // search by field (..date_format(field1, '%x%v')=:201807 && field2!=:TestNot && Alias.field3:TestIsWithAlias..)
        } else if($position = strpos($term, ':') !== false) {
            list($field, $value) = explode(':', $term);

            // find operator
            switch(true) {
                // not equals
                case $this->matchingEnds($field, '!='):
                    $field = rtrim($field, '!=');
                    $aliased = $this->buildAlias($field);
                    $expression = function($exp, $q) use($aliased, $value) {
                        return $value === 'null' ? $exp->isNotNull($aliased) : $exp->notEq($aliased, $value);
                    };
                break;

                // equals
                case $this->matchingEnds($field, '='):
                    $field = rtrim($field, '=');
                default:
                    $aliased = $this->buildAlias($field);
                    $expression = function($exp, $q) use($aliased, $value) {
                        return $value === 'null' ? $exp->isNull($aliased) : $exp->eq($aliased, $value);
                    };
                break;
            }

            // build hightlight and condition
            $this->buildHighlight($field, $value);
            $conditions = $expression;

        // search by text (..Test..)
        } else {
            foreach($this->fields as $field) {
                $value = $term;
                $this->buildHighlight($field, $value);
                $conditions[] = function($exp, $q) use($field, $value) {
                    return $exp->like("{$this->alias}.{$field}", "%{$value}%");
                };
            }
        }

        // finish with `OR`
        return ['OR' => $conditions];
    }

    /**
     * String ends with string?
     * @param string $s1
     * @param string $s2
     * @return boolean
     */
    private function matchingEnds($s1, $s2) {
        return substr($s1, -strlen($s2)) === $s2;
    }

    /**
     * Build field alias
     * @param string $field
     * @return string
     */
    private function buildAlias($field) {
        // field already aliased?
        if(strpos($field, '.') !== false)
            return $field;

        // field is inside a function
        if(strpos($field, '(') !== false)
            return preg_replace('/(\((?!.*\())/', "({$this->alias}.", $field);

        // build alias
        return "{$this->alias}.{$field}";
    }

    /**
     * Build hightlight array
     * @param string $field
     * @param string $value
     */
    private function buildHighlight($field, $value) {
        // default, hightlight is field
        $highlight = $field;

        // field is inside a function
        if(strpos($field, '(') !== false) {
            $highlight = substr(strrchr(rtrim($field, ')'), '('), 1);
            if(strpos($highlight, ',') !== false) // got multiple arguments? get the 1st one
                $highlight = substr($highlight, 0 , (strpos($highlight, ',')));
        }

        // strip alias
        $highlight = strpos($highlight, '.') !== false ? substr(strrchr($highlight, '.'), 1) : $highlight;

        // build hightlight
        $this->highlight[$highlight][] = $value;
    }
}
