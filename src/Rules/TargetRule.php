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
 * An extension of the rule class for the abstract parent of target rule classes.
 *
 * @package SilverWare\Validator\Rules
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware-validator
 */
abstract class TargetRule extends Rule
{
    /**
     * Defines the default format for the rule.
     *
     * @var string
     * @config
     */
    private static $default_format = '{TargetFieldID}';
    
    /**
     * The name of the target field.
     *
     * @var string
     */
    protected $target;
    
    /**
     * Constructs the object upon instantiation.
     *
     * @param string $target
     */
    public function __construct($target = null)
    {
        // Construct Parent:
        
        parent::__construct();
        
        // Construct Object:
        
        $this->setTarget($target);
    }
    
    /**
     * Defines the value of the target attribute.
     *
     * @param string $target
     *
     * @return $this
     */
    public function setTarget($target)
    {
        $this->target = (string) $target;
        
        return $this;
    }
    
    /**
     * Answers the value of the target attribute.
     *
     * @return string
     */
    public function getTarget()
    {
        return $this->target;
    }
    
    /**
     * Answers the form field instance with the target name.
     *
     * @return FormField
     */
    public function getTargetField()
    {
        return $this->getDataField($this->target);
    }
    
    /**
     * Answers the title of the target field.
     *
     * @return mixed
     */
    public function getTargetTitle()
    {
        return $this->getTargetField()->Title();
    }
    
    /**
     * Answers the data value of the target field.
     *
     * @return mixed
     */
    public function getTargetValue()
    {
        return $this->getTargetField()->dataValue();
    }
    
    /**
     * Answers the ID of the target field instance.
     *
     * @return string
     */
    public function getTargetFieldID()
    {
        return sprintf('#%s', $this->getTargetField()->getAttribute('id'));
    }
}
