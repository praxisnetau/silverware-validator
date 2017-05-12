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

/**
 * An extension of the min rule class for a mincheck rule.
 *
 * @package SilverWare\Validator\Rules
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware-validator
 */
class MinCheckRule extends MinRule
{
    /**
     * Defines the default type for the rule.
     *
     * @var string
     * @config
     */
    private static $default_type = 'mincheck';
    
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
        
        return (count($value) >= $this->min);
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
                'You must select a minimum of %d options.'
            ),
            $this->min
        );
    }
}
