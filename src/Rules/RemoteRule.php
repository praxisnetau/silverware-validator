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

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use SilverStripe\Core\Convert;
use SilverStripe\Control\Director;
use SilverWare\Validator\Rule;

/**
 * An extension of the rule class for a remote rule.
 *
 * By default, Parsley will consider any 2xx response code as valid.
 * Alternatively, you can use the 'reverse' option for $remoteValidator,
 * and Parsley will consider any 2xx response code as invalid.
 *
 * @package SilverWare\Validator\Rules
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware-validator
 */
class RemoteRule extends Rule
{
    /**
     * Defines the default type for the rule.
     *
     * @var string
     * @config
     */
    private static $default_type = 'remote';
    
    /**
     * Defines the default format for the rule.
     *
     * @var string
     * @config
     */
    private static $default_format = '{URLWithParams}';
    
    /**
     * The remote URL used to validate the value.
     *
     * @var string
     */
    protected $url;
    
    /**
     * An array of parameters to pass to the remote validator.
     *
     * @var array
     */
    protected $params = [];
    
    /**
     * An array of options for the validator.
     *
     * @var array
     */
    protected $options = [];
    
    /**
     * The remote validator to use; Parsley ships with with 'default' and 'reverse'.
     *
     * @var string
     */
    protected $remoteValidator;
    
    /**
     * Constructs the object upon instantiation.
     *
     * @param string $url
     * @param array $params
     * @param array $options
     * @param string $remoteValidator
     */
    public function __construct($url = null, $params = [], $options = [], $remoteValidator = 'default')
    {
        // Construct Parent:
        
        parent::__construct();
        
        // Construct Object:
        
        $this->setURL($url);
        $this->setParams($params);
        $this->setOptions($options);
        $this->setRemoteValidator($remoteValidator);
    }
    
    /**
     * Defines the value of the url attribute.
     *
     * @param string $url
     *
     * @return $this
     */
    public function setURL($url)
    {
        $this->url = (string) $url;
        
        return $this;
    }
    
    /**
     * Answers the value of the url attribute.
     *
     * @return string
     */
    public function getURL()
    {
        return $this->url;
    }
    
    /**
     * Answers the absolute URL of the receiver.
     *
     * @return string
     */
    public function getAbsoluteURL()
    {
        return Director::is_relative_url($this->url) ? Director::absoluteURL($this->url) : $this->url; 
    }
    
    /**
     * Defines a parameter with the specified name and value.
     *
     * @param string $name
     * @param mixed $value
     * 
     * @return $this
     */
    public function setParam($name, $value)
    {
        $this->params[$name] = $value;
        
        return $this;
    }
    
    /**
     * Answers the value of a parameter with the specified name.
     *
     * @param string $name
     * 
     * @return mixed
     */
    public function getParam($name)
    {
        if ($this->hasParam($name)) {
            return $this->params[$name];
        }
    }
    
    /**
     * Answers true if the receiver has a parameter with the specified name.
     *
     * @param string $name
     * 
     * @return boolean
     */
    public function hasParam($name)
    {
        return isset($this->params[$name]);
    }
    
    /**
     * Defines the value of the params attribute.
     *
     * @param array $params
     *
     * @return $this
     */
    public function setParams($params)
    {
        $this->params = (array) $params;
        
        return $this;
    }
    
    /**
     * Answers the value of the params attribute.
     *
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }
    
    /**
     * Defines the value of the options attribute.
     *
     * @param array $options
     *
     * @return $this
     */
    public function setOptions($options)
    {
        $this->options = (array) $options;
        
        return $this;
    }
    
    /**
     * Answers the value of the options attribute.
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }
    
    /**
     * Defines the value of the remoteValidator attribute.
     *
     * @param string $remoteValidator
     *
     * @return $this
     */
    public function setRemoteValidator($remoteValidator)
    {
        $this->remoteValidator = (string) $remoteValidator;
        
        return $this;
    }
    
    /**
     * Answers the value of the remoteValidator attribute.
     *
     * @return string
     */
    public function getRemoteValidator()
    {
        return $this->remoteValidator;
    }
    
    /**
     * Answers the defined URL with parameters included.
     *
     * @return string
     */
    public function getURLWithParams()
    {
        return ($this->params ? sprintf('%s?%s', $this->url, http_build_query($this->params)) : $this->url);
    }
    
    /**
     * Answers the HTTP request method used to call the remote validator.
     *
     * @return string
     */
    public function getMethod()
    {
        return isset($this->options['type']) ? $this->options['type'] : 'GET';
    }
    
    /**
     * Answers the validator attributes for the associated form field.
     *
     * @return array
     */
    public function getAttributes()
    {
        $attributes = parent::getAttributes();
        
        if ($this->options) {
            $attributes['remote-options'] = Convert::array2json($this->options);
        }
        
        if ($this->remoteValidator) {
            $attributes['remote-validator'] = $this->remoteValidator;
        }
        
        return $attributes;
    }
    
    /**
     * Answers true if the rule is valid for use with the validator.
     *
     * @return boolean
     */
    public function isValid()
    {
        return parent::isValid() && !is_null($this->url);
    }
    
    /**
     * Answers true if the validator is configured as 'reverse'.
     *
     * @return boolean
     */
    public function isReverse()
    {
        return ($this->getRemoteValidator() == 'reverse');
    }
    
    /**
     * Answers the test result of the validator rule on the given value.
     *
     * @param mixed $value
     *
     * @throws RequestException
     *
     * @return boolean
     */
    public function test($value)
    {
        // Answer Early (if necessary):
        
        if (!$this->isValid() || !$value) {
            return true;
        }
        
        // Initialise:
        
        $code = null;
        
        // Define Parameters:
        
        $this->setParam($this->getFieldName(), $value);
        
        // Create Guzzle Client:
        
        $client = new Client();
        
        // Attempt Request:
        
        try {
            
            // Obtain Response:
            
            $response = $client->request(
                $this->getMethod(),
                $this->getAbsoluteURL(),
                $this->getClientOptions()
            );
            
            // Obtain Status Code:
            
            $code = $response->getStatusCode();
            
        } catch (RequestException $e) {
            
            // Obtain Response:
            
            if ($e->hasResponse()) {
                
                // Obtain Status Code:
                
                $code = $e->getResponse()->getStatusCode();
                
            } else {
                
                // Throw Again (no response received):
                
                throw $e;
                
            }
            
        }
        
        // Answer Result:
        
        return $this->isValidStatusCode($code);
    }
    
    /**
     * Answers true if the given status code is considered valid.
     *
     * @param integer $code
     *
     * @return boolean
     */
    public function isValidStatusCode($code)
    {
        $valid = ($code >= 200 && $code < 300);
        
        return ($this->isReverse() ? !$valid : $valid);
    }
    
    /**
     * Answers an array of options for the HTTP client object.
     *
     * @return array
     */
    public function getClientOptions()
    {
        return [
            ($this->getMethod() == 'GET' ? 'query' : 'form_params') => $this->getParams()
        ];
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
