<?php

/**
 * This file is part of SilverWare.
 *
 * PHP version >=5.6.0
 *
 * For full copyright and license information, please view the
 * LICENSE.md file that was distributed with this source code.
 *
 * @package SilverWare\Validator\Extensions
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware-validator
 */

namespace SilverWare\Validator\Extensions;

use SilverStripe\Core\Extension;
use SilverWare\Validator\Validator;

/**
 * An extension which adds validator functionality to form field instances.
 *
 * @package SilverWare\Validator\Extensions
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware-validator
 */
class FormFieldExtension extends Extension
{
    /**
     * Updates the HTML attributes of the extended field.
     *
     * @param array $attributes
     */
    public function updateAttributes(&$attributes)
    {
        if ($validator = $this->getValidator()) {
            
            if ($extra = $validator->getAttributesForField($this->owner)) {
                $attributes = array_merge($attributes, $extra);
            }
            
        }
    }
    
    /**
     * Answers an array of the validator rules for the extended field.
     *
     * @return array
     */
    public function getValidatorRules()
    {
        if ($validator = $this->getValidator()) {
            return $validator->getRulesForField($this->owner);
        }
        
        return [];
    }
    
    /**
     * Answers an array of the validator messages for the extended field.
     *
     * @return array
     */
    public function getValidatorMessages()
    {
        $messages = [];
        
        foreach ($this->owner->getValidatorRules() as $rule) {
            
            if ($rule->hasMessage()) {
                $messages[$rule->getType()] = $rule->getMessage();
            }
            
        }
        
        return $messages;
    }
    
    /**
     * Answers the number of validator rules for the extended field.
     *
     * @return integer
     */
    public function getValidatorRuleCount()
    {
        return count($this->owner->getValidatorRules());
    }
    
    /**
     * Answers the validator instance from the associated form.
     *
     * @return Validator
     */
    protected function getValidator()
    {
        if ($form = $this->owner->getForm()) {
            
            if (($validator = $form->getValidator()) && $validator instanceof Validator) {
                return $validator;
            }
            
        }
    }
}
