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
 * An extension of the rule class for a max rule.
 *
 * @package SilverWare\Validator\Rules
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware-validator
 */
class MaxRule extends Rule
{
    /**
     * Defines the default type for the rule.
     *
     * @var string
     * @config
     */
    private static $default_type = 'max';
    
    /**
     * Defines the default format for the rule.
     *
     * @var string
     * @config
     */
    private static $default_format = '{max}';
    
    /**
     * The maximum value allowed.
     *
     * @var float
     */
    protected $max;
    
    /**
     * Constructs the object upon instantiation.
     *
     * @param float $max
     */
    public function __construct($max = null)
    {
        // Construct Parent:
        
        parent::__construct();
        
        // Construct Object:
        
        $this->max = $max;
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
     * Answers true if the rule is valid for use with the validator.
     *
     * @return boolean
     */
    public function isValid()
    {
        return parent::isValid() && !is_null($this->max);
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
        
        return ($value <= $this->max);
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
                'This value should be less than or equal to %s.'
            ),
            $this->max
        );
    }
}
