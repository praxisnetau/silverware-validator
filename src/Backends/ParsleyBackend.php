<?php

/**
 * This file is part of SilverWare.
 *
 * PHP version >=5.6.0
 *
 * For full copyright and license information, please view the
 * LICENSE.md file that was distributed with this source code.
 *
 * @package SilverWare\Validator\Backends
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware-validator
 */

namespace SilverWare\Validator\Backends;

use SilverStripe\Forms\CheckboxSetField;
use SilverStripe\Forms\Form;
use SilverStripe\Forms\FormField;
use SilverWare\Validator\Backend;

/**
 * An extension of the backend class which uses Parsley to handle form validation.
 *
 * @package SilverWare\Validator\Backends
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware-validator
 */
class ParsleyBackend extends Backend
{
    /**
     * An array of required JavaScript files.
     *
     * @var array
     * @config
     */
    private static $required_js = [
        'silverware-validator/client/dist/js/parsley.js'
    ];
    
    /**
     * Defines the default events which will trigger validation.
     *
     * @var string
     * @config
     */
    private static $default_trigger_on = 'change';
    
    /**
     * Defines the class name of the field group element.
     *
     * @var string
     * @config
     */
    private static $group_class = 'form-group';
    
    /**
     * Defines the class name of the error wrapper element.
     *
     * @var string
     * @config
     */
    private static $error_wrapper_class = 'form-control-feedback';
    
    /**
     * Defines the class name to add to the field group element upon error.
     *
     * @var string
     * @config
     */
    private static $group_error_class = 'has-danger';
    
    /**
     * Defines the class name to add to the field group element upon success.
     *
     * @var string
     * @config
     */
    private static $group_success_class = 'has-success';
    
    /**
     * Defines the class name to add to the field element upon error.
     *
     * @var string
     * @config
     */
    private static $field_error_class = 'form-control-danger';
    
    /**
     * Defines the class name to add to the field element upon success.
     *
     * @var string
     * @config
     */
    private static $field_success_class = 'form-control-success';
    
    /**
     * A string of events which will trigger validation.
     *
     * @var string
     */
    protected $triggerOn;
    
    /**
     * Answers an array of the validator attributes for the given form.
     *
     * @param Form $form
     *
     * @return array
     */
    public function getAttributesForForm(Form $form)
    {
        // Obtain Attributes (from parent):
        
        $attributes = parent::getAttributesForForm($form);
        
        // Define Group Class:
        
        $attributes['data-group-class'] = $this->config()->group_class;
        
        // Define Error Wrapper Class:
        
        $attributes['data-error-wrapper-class'] = $this->config()->error_wrapper_class;
        
        // Define Error Classes:
        
        $attributes['data-group-error-class']   = $this->config()->group_error_class;
        $attributes['data-field-error-class']   = $this->config()->field_error_class;
        
        // Define Success Classes:
        
        $attributes['data-group-success-class'] = $this->config()->group_success_class;
        $attributes['data-field-success-class'] = $this->config()->field_success_class;
        
        // Define Trigger Events:
        
        $attributes['data-parsley-trigger'] = $this->getTriggerOn();
        
        // Answer Attributes:
        
        return $this->flatten($attributes);
    }
    
    /**
     * Answers an array of the validator attributes for the given form field.
     *
     * @param FormField $field
     *
     * @return array
     */
    public function getAttributesForField(FormField $field)
    {
        // Obtain Attributes (from parent):
        
        $attributes = parent::getAttributesForField($field);
        
        // Handle Checkbox Set Fields:
        
        if ($field instanceof CheckboxSetField) {
            $attributes[$this->prefix('multiple')] = $field->getName();
        }
        
        // Merge Rule Attributes:
        
        if ($rules = $field->getValidatorRules()) {
            
            foreach ($rules as $rule) {
                
                // Check Rule Validity:
                
                if ($rule->isValid()) {
                    
                    // Merge Rule Attribute:
                    
                    $attributes[$this->prefix($rule->getAttribute())][] = $rule->getValue();
                    
                    // Merge Message Attribute:
                    
                    if ($rule->hasMessage()) {
                        $attributes[$this->attr('message', $rule->getAttribute())] = $rule->getMessage();
                    }
                    
                }
                
                // Merge Extra Rule Attributes:
                
                foreach ($rule->getAttributes() as $name => $value) {
                    $attributes[$this->attr($name)][] = $value;
                }
                
            }
            
        }
        
        // Answer Attributes:
        
        return $this->flatten($attributes);
    }
    
    /**
     * Defines the value of the triggerOn attribute.
     *
     * @param string|array $triggerOn
     *
     * @return $this
     */
    public function setTriggerOn($triggerOn)
    {
        $this->triggerOn = is_array($triggerOn) ? implode(' ', $triggerOn) : (string) $triggerOn;
        
        return $this;
    }
    
    /**
     * Answers the value of the triggerOn attribute, or the default trigger.
     *
     * @return string
     */
    public function getTriggerOn()
    {
        if ($this->triggerOn) {
            return $this->triggerOn;
        }
        
        return $this->config()->default_trigger_on;
    }
}
