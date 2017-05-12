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
 * An extension of the rule class for a pattern rule.
 *
 * @package SilverWare\Validator\Rules
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware-validator
 */
class PatternRule extends Rule
{
    /**
     * Defines the default type for the rule.
     *
     * @var string
     * @config
     */
    private static $default_type = 'pattern';
    
    /**
     * Defines the default format for the rule.
     *
     * @var string
     * @config
     */
    private static $default_format = '{pattern}';
    
    /**
     * The regular expression pattern used to test the value.
     *
     * @var string
     */
    protected $pattern;
    
    /**
     * Constructs the object upon instantiation.
     *
     * @param string $pattern
     */
    public function __construct($pattern = null)
    {
        // Construct Parent:
        
        parent::__construct();
        
        // Construct Object:
        
        $this->setPattern($pattern);
    }
    
    /**
     * Defines the value of the pattern attribute.
     *
     * @param string $pattern
     *
     * @return $this
     */
    public function setPattern($pattern)
    {
        $this->pattern = (string) $pattern;
        
        return $this;
    }
    
    /**
     * Answers the value of the pattern attribute.
     *
     * @return string
     */
    public function getPattern()
    {
        return $this->pattern;
    }
    
    /**
     * Answers true if the rule is valid for use with the validator.
     *
     * @return boolean
     */
    public function isValid()
    {
        return parent::isValid() && $this->pattern;
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
        if (!$this->isValid() || !$value) {
            return true;
        }
        
        return (boolean) preg_match($this->pattern, $value);
    }
    
    /**
     * Answers the default message for the rule.
     *
     * @return string
     */
    public function getDefaultMessage()
    {
        return _t(__CLASS__ . '.DEFAULTMESSAGE', 'This value seems to be invalid.');
    }
}
