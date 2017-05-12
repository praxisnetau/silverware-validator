<?php

/**
 * This file is part of SilverWare.
 *
 * PHP version >=5.6.0
 *
 * For full copyright and license information, please view the
 * LICENSE.md file that was distributed with this source code.
 *
 * @package SilverWare\Validator
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware-validator
 */

namespace SilverWare\Validator;

use SilverStripe\Forms\Form;
use SilverStripe\Forms\FormField;
use SilverStripe\Forms\Validator as BaseValidator;
use SilverStripe\ORM\ArrayLib;
use SilverStripe\View\Requirements;
use SilverWare\Validator\Rules\RequiredRule;

/**
 * An extension of the SilverStripe validator class for the SilverWare validator.
 *
 * @package SilverWare\Validator
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware-validator
 */
class Validator extends BaseValidator
{
    /**
     * Defines the injector dependencies for this object.
     *
     * @var array
     * @config
     */
    private static $dependencies = [
        'backend' => '%$ValidatorBackend'
    ];
    
    /**
     * Holds the rules defined for this validator.
     *
     * @var array
     */
    protected $rules = [];
    
    /**
     * If true, validation is performed client-side using JavaScript.
     *
     * @var boolean
     */
    protected $clientSide = true;
    
    /**
     * If true, validation is performed server-side using PHP.
     *
     * @var boolean
     */
    protected $serverSide = true;
    
    /**
     * Defines the value of the clientSide attribute.
     *
     * @param boolean $clientSide
     *
     * @return $this
     */
    public function setClientSide($clientSide)
    {
        $this->clientSide = (boolean) $clientSide;
        
        return $this;
    }
    
    /**
     * Answers the value of the clientSide attribute.
     *
     * @return boolean
     */
    public function getClientSide()
    {
        return $this->clientSide;
    }
    
    /**
     * Defines the value of the serverSide attribute.
     *
     * @param boolean $serverSide
     *
     * @return $this
     */
    public function setServerSide($serverSide)
    {
        $this->serverSide = (boolean) $serverSide;
        
        return $this;
    }
    
    /**
     * Answers the value of the serverSide attribute.
     *
     * @return boolean
     */
    public function getServerSide()
    {
        return $this->serverSide;
    }
    
    /**
     * Answers the validator backend in use for this validator.
     *
     * @return Backend
     */
    public function getBackend()
    {
        return $this->backend;
    }
    
    /**
     * Associates the form and data field instances with the validator and rules.
     *
     * @param Form $form
     *
     * @return $this
     */
    public function setForm($form)
    {
        // Associate Form Instance (via parent):
        
        parent::setForm($form);
        
        // Associate Rules with Fields:
        
        foreach ($this->rules as $fieldName => $rules) {
            
            if ($field = $this->getDataField($fieldName)) {
                
                foreach ($rules as $rule) {
                    $rule->setField($field);
                }
                
            }
            
        }
        
        // Associate Backend with Self:
        
        $this->backend->setFrontend($this);
        
        // Initialise Backend:
        
        $this->backend->doInit();
        
        // Define Form Classes:
        
        if ($classes = $this->getClassesForForm($form)) {
            
            foreach ($classes as $class) {
                $form->addExtraClass($class);
            }
            
        }
        
        // Define Form Attributes:
        
        if ($attributes = $this->getAttributesForForm($form)) {
            
            foreach ($attributes as $key => $value) {
                $form->setAttribute($key, $value);
            }
            
        }
        
        // Answer Self:
        
        return $this;
    }
    
    /**
     * Answers the list of fields from the associated form.
     *
     * @return FieldList
     */
    public function getFormFields()
    {
        return $this->form->Fields();
    }
    
    /**
     * Answers a data field with the specified name.
     *
     * @param string $name
     *
     * @return FormField
     */
    public function getDataField($name)
    {
        return $this->getFormFields()->dataFieldByName($name);
    }
    
    /**
     * Sets the given rule for the form field with the given name.
     *
     * @param string $fieldName
     * @param Rule $rule
     *
     * @return $this
     */
    public function setRule($fieldName, Rule $rule)
    {
        $this->rules[$fieldName][$rule->class] = $rule;
        
        $rule->setValidator($this);
        
        return $this;
    }
    
    /**
     * Sets the rules for the form field with the given name from the given array, or the entire list of rules.
     *
     * @param string|array $fieldNameOrArray
     * @param array $rules
     *
     * @return $this
     */
    public function setRules($fieldNameOrArray, $rules = [])
    {
        // Determine Parameter Mode:
        
        if (is_array($fieldNameOrArray)) {
            
            // Define All Rules:
            
            foreach ($fieldNameOrArray as $fieldName => $rules) {
                $this->setRules($fieldName, $rules);
            }
            
        } else {
            
            // Define Rules for Specified Field:
            
            foreach ($rules as $rule) {
                $this->setRule($fieldNameOrArray, $rule);
            }
            
        }
        
        // Answer Self:
        
        return $this;
    }
    
    /**
     * Answers an array containing the rules for the given form field object.
     *
     * @param FormField $field
     *
     * @return array
     */
    public function getRulesForField(FormField $field)
    {
        return $this->getRulesForFieldName($field->getName());
    }
    
    /**
     * Answers an array containing the rules for the field with the given name.
     *
     * @param string $fieldName
     *
     * @return array
     */
    public function getRulesForFieldName($fieldName)
    {
        if (isset($this->rules[$fieldName])) {
            return $this->rules[$fieldName];
        }
        
        return [];
    }
    
    /**
     * Answers the validator classes for the given form.
     *
     * @param Form $form
     *
     * @return array
     */
    public function getClassesForForm(Form $form)
    {
        return $this->backend->getClassesForForm($form);
    }
    
    /**
     * Answers the validator attributes for the given form.
     *
     * @param Form $form
     *
     * @return array
     */
    public function getAttributesForForm(Form $form)
    {
        return $this->backend->getAttributesForForm($form);
    }
    
    /**
     * Answers the validator attributes for the given form field.
     *
     * @param FormField $field
     *
     * @return array
     */
    public function getAttributesForField(FormField $field)
    {
        return $this->backend->getAttributesForField($field);
    }
    
    /**
     * Adds a required rule for the form field with the specified name.
     *
     * @param string $fieldName
     * @param string $message
     *
     * @return $this
     */
    public function addRequiredField($fieldName, $message = null)
    {
        return $this->setRule($fieldName, RequiredRule::create($message));
    }
    
    /**
     * Adds required rules for each of the field names present in the given array (associative messages optional).
     *
     * @param array $fields
     *
     * @return $this
     */
    public function addRequiredFields($fields)
    {
        if (ArrayLib::is_associative($fields)) {
            
            foreach ($fields as $name => $message) {
                $this->addRequiredField($name, $message);
            }
            
        } else {
            
            foreach ($fields as $name) {
                $this->addRequiredField($name);
            }
            
        }
        
        return $this;
    }
    
    /**
     * Answers true if the field with the given name is required.
     *
     * @param string $fieldName
     *
     * @return boolean
     */
    public function fieldIsRequired($fieldName)
    {
        if ($rules = $this->getRulesForFieldName($fieldName)) {
            
            foreach ($rules as $rule) {
                
                if ($rule instanceof RequiredRule) {
                    return true;
                }
                
            }
            
        }
        
        return parent::fieldIsRequired($fieldName);
    }
    
    /**
     * Uses the validator backend to configure the given rule.
     *
     * @param Rule $rule
     *
     * @return Rule
     */
    public function configureRule(Rule $rule)
    {
        return $this->backend->configureRule($rule);
    }
    
    /**
     * Performs server-side validation of the submitted form data.
     *
     * @param array $data
     *
     * @return boolean
     */
    public function php($data)
    {
        // Is Server-side Validation Enabled?
        
        if (!$this->getServerSide()) {
            return true;
        }
        
        // Initialise:
        
        $valid = true;
        
        // Validate Fields:
        
        foreach ($this->getFormFields() as $field) {
            $valid = ($field->validate($this) && $valid);
        }
        
        // Validate Rules:
        
        foreach ($this->rules as $fieldName => $rules) {
            
            if ($field = $this->getDataField($fieldName)) {
                
                foreach ($rules as $rule) {
                    
                    // Test Field Data:
                    
                    if (!$rule->test($data[$fieldName])) {
                        $this->validationError($fieldName, $rule->getMessage(), 'validation');
                        $valid = false;
                    }
                    
                }
                
            }
            
        }
        
        // Answer Result:
        
        return $valid;
    }
}
