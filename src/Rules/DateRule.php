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
 * An extension of the rule class for a date rule.
 *
 * @package SilverWare\Validator\Rules
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware-validator
 */
class DateRule extends Rule
{
    /**
     * Defines the default type for the rule.
     *
     * @var string
     * @config
     */
    private static $default_type = 'date';
    
    /**
     * Defines the default format for the rule.
     *
     * @var string
     * @config
     */
    private static $default_format = '{ClientFormat}';
    
    /**
     * Default client-side date format to validate, uses Moment.js format.
     *
     * @var string
     * @config
     */
    private static $default_client_format = 'YYYY-MM-DD';
    
    /**
     * Date format mappings, native PHP on the left, Moment.js on the right.
     *
     * @var array
     * @config
     */
    private static $format_mappings = [
        'd' => 'DD',
        'D' => 'ddd',
        'j' => 'D',
        'l' => 'dddd',
        'N' => 'E',
        'S' => 'o',
        'w' => 'e',
        'z' => 'DDD',
        'W' => 'W',
        'F' => 'MMMM',
        'm' => 'MM',
        'M' => 'MMM',
        'n' => 'M',
        'Y' => 'YYYY',
        'y' => 'YY',
        'a' => 'a',
        'A' => 'A',
        'g' => 'h',
        'G' => 'H',
        'h' => 'hh',
        'H' => 'HH',
        'i' => 'mm',
        's' => 'ss',
        'u' => 'SSS',
        'e' => 'zz',
        'U' => 'X'
    ];
    
    /**
     * Client-side date format to validate.
     *
     * @var string
     */
    protected $clientFormat;
    
    /**
     * Server-side date format to validate.
     *
     * @var string
     */
    protected $serverFormat;
    
    /**
     * Constructs the object upon instantiation.
     *
     * @param string $clientFormat Client-side date format (uses Moment.js format).
     * @param string $serverFormat Server-side date format (if empty, will use converted client-side date format).
     */
    public function __construct($clientFormat = null, $serverFormat = null)
    {
        // Construct Parent:
        
        parent::__construct();
        
        // Construct Object:
        
        $this->setClientFormat($clientFormat ? $clientFormat : $this->config()->default_client_format);
        $this->setServerFormat($serverFormat);
    }
    
    /**
     * Defines the value of the clientFormat attribute.
     *
     * @param string $clientFormat
     *
     * @return $this
     */
    public function setClientFormat($clientFormat)
    {
        $this->clientFormat = (string) $clientFormat;
        
        return $this;
    }
    
    /**
     * Answers the value of the clientFormat attribute.
     *
     * @return string
     */
    public function getClientFormat()
    {
        return $this->clientFormat;
    }
    
    /**
     * Answers true if a client format is defined.
     *
     * @return boolean
     */
    public function hasClientFormat()
    {
        return (boolean) $this->getClientFormat();
    }
    
    /**
     * Translates the client-side format into a native PHP date format.
     *
     * @return string
     */
    public function getClientFormatAsPHP()
    {
        return strtr($this->clientFormat, array_flip($this->config()->format_mappings));
    }
    
    /**
     * Defines the value of the serverFormat attribute.
     *
     * @param string $serverFormat
     *
     * @return $this
     */
    public function setServerFormat($serverFormat)
    {
        $this->serverFormat = (string) $serverFormat;
        
        return $this;
    }
    
    /**
     * Answers the value of the serverFormat attribute.
     *
     * @return string
     */
    public function getServerFormat()
    {
        return $this->serverFormat;
    }
    
    /**
     * Answers true if a server format is defined.
     *
     * @return boolean
     */
    public function hasServerFormat()
    {
        return (boolean) $this->getServerFormat();
    }
    
    /**
     * Answers the server-side format as PHP (either as defined, or converted from client-side format).
     *
     * @return string
     */
    public function getServerFormatAsPHP()
    {
        return $this->hasServerFormat() ? $this->getServerFormat() : $this->getClientFormatAsPHP();
    }
    
    /**
     * Answers true if the rule is valid for use with the validator.
     *
     * @return boolean
     */
    public function isValid()
    {
        return parent::isValid() && $this->hasClientFormat();
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
        if ($value && $info = $this->getDateInfo($value)) {
            return ($info['error_count'] == 0 && $info['warning_count'] == 0);
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
        return _t(__CLASS__ . '.DEFAULTMESSAGE', 'This value appears to be an invalid date.');
    }
    
    /**
     * Answers an array containing information about the given date value.
     *
     * @param string $value
     *
     * @return array
     */
    protected function getDateInfo($value)
    {
        return date_parse_from_format($this->getServerFormatAsPHP(), $value);
    }
}
