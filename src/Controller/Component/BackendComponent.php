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
 * @version 1.1
 */
class BackendComponent extends Component
{
    /**
     * {@inheritDoc}
     * @see \Cake\Controller\Component::initialize()
     */
    public function initialize(array $config) {
        parent::initialize($config);

        // load security
        if(Configure::read('Backend.security.enabled')) {
            $this->getController()->loadComponent('Security');
            if(Configure::read('Backend.security.ssl'))
                $this->getController()->Security->requireSecure();
            $this->getController()->loadComponent('Csrf', [
                'httpOnly' => true,
                'secure' => env('HTTPS')
            ]);
        }

        // load required components
        $this->getController()->loadComponent('Unimatrix/Backend.Flash');
        $this->getController()->loadComponent('Unimatrix/Backend.Auth');
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
                    // moment widget
                    if(substr($name, 0, 12) === '_fake_input_') {
                        $dirty = true;
                        unset($body[$name]);
                        $real = substr($name, 12);
                        if($body[$real])
                            $body[$real] = new FrozenTime($body[$real]);
                    }

                    // picky & media widget
                    if(is_array($value) && in_array('_to_empty_array_', $value, true)) {
                        $dirty = true;
                        $body[$name] = [];
                    }
                }

                // request changed, overwrite request
                if($dirty)
                    $this->getController()->request = $request->withParsedBody($body);
            }
        }
    }

    /**
     * Variables for search
     */
    private $alias = null;
    private $fields = [];
    private $highlight = [];

    /**
     * Handle search for actions
     * @param string $alias The table alias
     * @param array $fields The like fields in case of text only search
     * @return array
     */
    public function search($alias, $fields = []) {
        // start
        $conditions = [];
        $search = new SearchForm();

        // set alias and fields
        $this->alias = $alias;
        $this->fields = $fields;

        // on post
        $request = $this->getController()->request;
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
            $this->getController()->redirect($target);

        // on get
        } else {
            // get term
            $term = $request->getQuery('search');
            if($term) {
                // fill value and execute form
                $this->getController()->request = $request->withData('search', $term);
                if($search->execute($this->getController()->request->getData())) {
                    // do conditions
                    if(strpos($term, '||') !== false || strpos($term, '&&') !== false) {
                        foreach(explode('||', $term) as $one) {
                            if(strpos($one, '&&') !== false) {
                                foreach(explode('&&', $one) as $two)
                                    $conditions['OR']['AND'][] = $this->compute($two);
                            } else $conditions['OR']['OR'][] = $this->compute($one);
                        }
                    } else $conditions = $this->compute($term);
                }
            }
        }

        // send to template
        $this->getController()->set('search', $search);
        $this->getController()->set('highlight', $this->highlight);

        // return computed conditions
        return $conditions;
    }

    /**
     * Compute conditions
     * @param string $term The search term
     * @return array of conditions with the `OR` key
     */
    private function compute($term) {
        // handle term
        $term = trim($term);
        if(!$term)
            return false;

        // start empty
        $conditions = [];

        // search by id
        if($term[0] == '#') {
            $conditions["{$this->alias}.id"] = $this->highlight['id'] = ltrim($term, '#');

        // search by field
        } else if(strpos($term, ':') !== false) {
            list($field, $value) = explode(':', $term);
            $conditions[strpos($field, '.') !== false ? $field :
                (strpos($field, '(') !== false ? preg_replace('/(\((?!.*\())/', "({$this->alias}.", $field) :
                    "{$this->alias}.{$field}")] = $value;
            $this->highlight[strpos($field, '(') !== false ? $this->alias(substr(strrchr(rtrim($field, ')'), '('), 1)) :
                $this->alias($field)][] = $value;

        // search by text
        } else {
            foreach($this->fields as $field) {
                $conditions["{$this->alias}.{$field} LIKE"] = "%{$term}%";
                $this->highlight[$field][] = $term;
            }
        }

        // finish with `OR`
        return ['OR' => $conditions];
    }

    /**
     * Strip alias from the field
     * @param string $field
     * @return string
     */
    private function alias($field) {
        return strpos($field, '.') !== false ? substr(strrchr($field, '.'), 1) : $field;
    }
}
