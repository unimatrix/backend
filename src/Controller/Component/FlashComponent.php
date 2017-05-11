<?php

namespace Unimatrix\Backend\Controller\Component;

use Cake\Controller\Component\FlashComponent as CakeFlashComponent;

/**
 * Flash component
 * Overwrite default Flash Component and use our own backend templates
 * These templates are sexy and require some javascript magic (from backend.js)
 *
 * @author Flavius
 * @version 0.1
 */
class FlashComponent extends CakeFlashComponent
{
    /**
     * {@inheritDoc}
     * @see \Cake\Controller\Component\FlashComponent::set()
     */
    public function set($message, array $options = []) {
        // add the plugin to our flash elements
        $config = $this->getConfig();
        $options['element'] = 'Unimatrix/Backend.' . ($options['element'] ?? $config['element']);

        // continue as normal
        parent::set($message, $options);
    }
}
