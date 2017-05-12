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

/**
 * An extension of the rule class for a range rule.
 *
 * @package SilverWare\Validator\Rules
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware-validator
 */
class RangeRule extends Rule
{
    /**
     * Defines the default type for the rule.
     *
     * @var string
     * @config
     */
    private static $default_type = 'range';
    
    /**
     * Defines the default format for the rule.
     *
     * @var string
     * @config
     */
    private static $default_format = '[{min}, {max}]';
    
    /**
     * The minimum value allowed.
     *
     * @var float
     */
    protected $min;
    
    /**
     * The maximum value allowed.
     *
     * @var float
     */
    protected $max;
    
    /**
     * Constructs the object upon instantiation.
     *
     * @param float $min
     * @param float $max
     */
    public function __construct($min = null, $max = null)
    {
        // Construct Parent:
        
        parent::__construct();
        
        // Construct Object:
        
        $this->min = $min;
        $this->max = $max;
    }
    
    /**
     * Defines the value of the min attribute.
     *
     * @param float $min
     *
     * @return $this
     */
    public function setMin($min)
    {
        $this->min = (float) $min;
        
        return $this;
    }
    
    /**
     * Answers the value of the min attribute.
     *
     * @return float
     */
    public function getMin()
    {
        return $this->min;
    }
    
    /**
     * Defines the value of the max attribute.
     *
     * @param float $max
     *
     * @return $this
     */
    public function setMax($max)
    {
        $this->max = (float) $max;
        
        return $this;
    }
    
    /**
     * Answers the value of the max attribute.
     *
     * @return float
     */
    public function getMax()
    {
        return $this->max;
    }
    
    /**
     * Defines the values of both the min and max attributes as a range.
     *
     * @param float $min
     * @param float $max
     *
     * @return $this
     */
    public function setRange($min, $max)
    {
        $this->setMin($min);
        $this->setMax($max);
        
        return $this;
    }
    
    /**
     * Answers true if the rule is valid for use with the validator.
     *
     * @return boolean
     */
    public function isValid()
    {
        return parent::isValid() && !is_null($this->min) && !is_null($this->max);
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
        if (!$this->isValid() || !is_numeric($value)) {
            return true;
        }
        
        return ($value >= $this->min && $value <= $this->max);
    }
    
    /**
     * Answers the default message for the rule.
     *
     * @return string
     */
    public function getDefaultMessage()
    {
        return sprintf(
            _t(
                __CLASS__ . '.DEFAULTMESSAGE',
                'This value should be between %s and %s.'
            ),
            $this->min,
            $this->max
        );
    }
}
