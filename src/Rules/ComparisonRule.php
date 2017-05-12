<?php

/**
 * This file is part of SilverWare.
 *
 * PHP version >=5.6.0
 *
 * For full copyright and license information, please view the
 * LICENSE.md file that was distributed with this source code.
 *
 * @package SilverWare\Validator\Rules
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware-validator
 */

namespace SilverWare\Validator\Rules;

use SilverWare\Validator\Rule;
use InvalidArgumentException;

/**
 * An extension of the target rule class for a comparison rule.
 *
 * @package SilverWare\Validator\Rules
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware-validator
 */
class ComparisonRule extends TargetRule
{
    /**
     * Define constants.
     */
    const LESS_THAN             = 'lt';
    const LESS_THAN_OR_EQUAL    = 'lte';
    const GREATER_THAN          = 'gt';
    const GREATER_THAN_OR_EQUAL = 'gte';
    
    /**
     * Constructs the object upon instantiation.
     *
     * @param string $type
     * @param string $target
     */
    public function __construct($type = null, $target = null)
    {
        // Construct Parent:
        
        parent::__construct($target);
        
        // Construct Object:
        
        $this->setType($type);
    }
    
    /**
     * Defines the value of the type attribute.
     *
     * @param string $type
     *
     * @throws InvalidArgumentException
     *
     * @return $this
     */
    public function setType($type)
    {
        $this->checkType($type);
        
        return parent::setType($type);
    }
    
    /**
     * Answers the test result of the validator rule on the given value.
     *
     * @param mixed $value
     *
     * @return boolean
     */
    public function test($value)
    {
        switch ($this->type) {
            
            case self::LESS_THAN:
                return ($value < $this->getTargetValue());
                
            case self::LESS_THAN_OR_EQUAL:
                return ($value <= $this->getTargetValue());
                
            case self::GREATER_THAN:
                return ($value > $this->getTargetValue());
                
            case self::GREATER_THAN_OR_EQUAL:
                return ($value >= $this->getTargetValue());
                
        }
        
        return true;
    }
    
    /**
     * Answers the default message for the rule.
     *
     * @return string
     */
    public function getDefaultMessage()
    {
        switch ($this->type) {
            
            case self::LESS_THAN:
                
                $message = _t(
                    __CLASS__ . '.DEFAULTMESSAGELESSTHAN',
                    'This value should be less than the value of the %s field.'
                );
                
                break;
                
            case self::LESS_THAN_OR_EQUAL:
                
                $message = _t(
                    __CLASS__ . '.DEFAULTMESSAGELESSTHANOREQUAL',
                    'This value should be less than or equal to the value of the %s field.'
                );
                
                break;
                
            case self::GREATER_THAN:
                
                $message = _t(
                    __CLASS__ . '.DEFAULTMESSAGEGREATERTHAN',
                    'This value should be greater than the value of the %s field.'
                );
                
                break;
                
            case self::GREATER_THAN_OR_EQUAL:
                
                $message = _t(
                    __CLASS__ . '.DEFAULTMESSAGEGREATERTHANOREQUAL',
                    'This value should be greater than or equal to the value of the %s field.'
                );
                
                break;
                
        }
        
        return sprintf($message, $this->getTargetTitle());
    }
    
    /**
     * Answers an array of valid comparison types.
     *
     * @return array
     */
    public function getValidTypes()
    {
        return [
            self::LESS_THAN,
            self::LESS_THAN_OR_EQUAL,
            self::GREATER_THAN,
            self::GREATER_THAN_OR_EQUAL
        ];
    }
    
    /**
     * Checks the validity of the specified comparison type.
     *
     * @param string $type
     *
     * @throws InvalidArgumentException
     *
     * @return void
     */
    protected function checkType($type)
    {
        if (!in_array($type, $this->getValidTypes())) {
            throw new InvalidArgumentException(sprintf('Invalid comparison type: %s', $type));
        }
    }
}
