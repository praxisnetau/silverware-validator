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
 * An extension of the target rule class for an equalto rule.
 *
 * @package SilverWare\Validator\Rules
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware-validator
 */
class EqualToRule extends TargetRule
{
    /**
     * Defines the default type for the rule.
     *
     * @var string
     * @config
     */
    private static $default_type = 'equalto';
    
    /**
     * Answers the test result of the validator rule on the given value.
     *
     * @param mixed $value
     *
     * @return boolean
     */
    public function test($value)
    {
        return ($value == $this->getTargetValue());
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
                'This value should be the same as the %s field.'
            ),
            $this->getTargetTitle()
        );
    }
}
