<?php

/**
 * This file is part of SilverWare.
 *
 * PHP version >=5.6.0
 *
 * For full copyright and license information, please view the
 * LICENSE.md file that was distributed with this source code.
 *
 * @package SilverWare\Validator
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware-validator
 */

namespace SilverWare\Validator;

use SilverStripe\Core\Config\Configurable;
use SilverStripe\Core\Extensible;
use SilverStripe\Core\Injector\Injectable;
use SilverStripe\Forms\FormField;

/**
 * An extension of the object class for the abstract parent class of validator rules.
 *
 * @package SilverWare\Validator
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware-validator
 */
abstract class Rule
{
    use Configurable;
    use Extensible;
    use Injectable;

    /**
     * Defines the default type for the rule.
     *
     * @var string
     * @config
     */
    private static $default_type;

    /**
     * Defines the default format for the rule.
     *
     * @var string
     * @config
     */
    private static $default_format;

    /**
     * A string which defines the rule type.
     *
     * @var string
     */
    protected $type;

    /**
     * The form field instance associated with this rule.
     *
     * @var FormField
     */
    protected $field;

    /**
     * A tokenised string which defines the format for the validator attribute.
     *
     * @var string
     */
    protected $format;

    /**
     * The message displayed to the user when the rule test fails.
     *
     * @var string
     */
    protected $message;

    /**
     * The form validator instance associated with this rule.
     *
     * @var Validator
     */
    protected $validator;

    /**
     * The name of the data attribute used for this rule.
     *
     * @var string|array
     */
    protected $attribute;

    /**
     * Constructs the object upon instantiation.
     */
    public function __construct()
    {

    }

    /**
     * Answers the test result of the rule on the given value.
     *
     * @param mixed $value
     *
     * @return boolean
     */
    abstract public function test($value);

    /**
     * Defines the value of the type attribute.
     *
     * @param string $type
     *
     * @return $this
     */
    public function setType($type)
    {
        $this->type = (string) $type;

        return $this;
    }

    /**
     * Answers the value of the type attribute.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type ? $this->type : $this->getDefaultType();
    }

    /**
     * Answers the default type for the receiver.
     *
     * @return string
     */
    public function getDefaultType()
    {
        return $this->config()->default_type;
    }

    /**
     * Answers true if the receiver has a type defined.
     *
     * @return boolean
     */
    public function hasType()
    {
        return (boolean) $this->type;
    }

    /**
     * Defines the value of the format attribute.
     *
     * @param string $format
     *
     * @return $this
     */
    public function setFormat($format)
    {
        $this->format = (string) $format;

        return $this;
    }

    /**
     * Answers the value of the format attribute.
     *
     * @return string
     */
    public function getFormat()
    {
        return $this->format ? $this->format : $this->getDefaultFormat();
    }

    /**
     * Answers true if the receiver has a format defined.
     *
     * @return boolean
     */
    public function hasFormat()
    {
        return (boolean) $this->getFormat();
    }

    /**
     * Answers the default format for the receiver.
     *
     * @return string
     */
    public function getDefaultFormat()
    {
        return $this->config()->default_format;
    }

    /**
     * Defines the name(s) used by the rule for the form field attribute.
     *
     * @param string|array $attribute
     *
     * @return $this
     */
    public function setAttribute($attribute)
    {
        $this->attribute = $attribute;

        return $this;
    }

    /**
     * Answers the name used by the rule for the form field attribute.
     *
     * @return string
     */
    public function getAttribute()
    {
        // Determine Attribute Type:

        if (is_array($this->attribute)) {

            // Answer Conditional Attribute:

            foreach ($this->attribute as $name => $cond) {

                list($attr, $val) = explode('=', $cond);

                if ($this->$attr == $val) {
                    return $name;
                }

            }

        } elseif ($this->attribute) {

            // Using Dynamic Attribute? (i.e. $type)

            if (strpos($this->attribute, '$') === 0) {

                $attr = substr($this->attribute, 1);

                return $this->$attr;

            }

            // Answer String Attribute:

            return $this->attribute;

        }

        // Answer Default Attribute:

        return $this->backend()->getDefaultAttribute();
    }

    /**
     * Answers the value of the rule attribute.
     *
     * @return string
     */
    public function getValue()
    {
        return $this->hasFormat() ? $this->getFormattedValue() : $this->getType();
    }

    /**
     * Answers the formatted value for the rule attribute.
     *
     * @return string
     */
    public function getFormattedValue()
    {
        if ($this->hasFormat()) {

            switch ($this->getFormat()) {
                case 'boolean':
                    return 'true';
                default:
                    return $this->replaceTokens($this->getFormat());
            }

        }
    }

    /**
     * Defines the value of the field attribute.
     *
     * @param FormField $field
     *
     * @return $this
     */
    public function setField(FormField $field)
    {
        $this->field = $field;

        return $this;
    }

    /**
     * Answers the value of the field attribute.
     *
     * @return FormField
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * Answers the name of the associated form field.
     *
     * @return string
     */
    public function getFieldName()
    {
        if ($field = $this->getField()) {
            return $field->getName();
        }
    }

    /**
     * Answers the form instance from the associated field.
     *
     * @return Form
     */
    public function getForm()
    {
        return $this->field->getForm();
    }

    /**
     * Answers the list of fields from the associated form.
     *
     * @return FieldList
     */
    public function getFormFields()
    {
        return $this->getForm()->Fields();
    }

    /**
     * Answers a data field with the specified name.
     *
     * @param string $name
     *
     * @return FormField
     */
    public function getDataField($name)
    {
        return $this->getFormFields()->dataFieldByName($name);
    }

    /**
     * Defines the value of the message attribute.
     *
     * @param string $message
     *
     * @return $this
     */
    public function setMessage($message)
    {
        $this->message = (string) $message;

        return $this;
    }

    /**
     * Answers the value of the message attribute.
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message ? $this->message : $this->getDefaultMessage();
    }

    /**
     * Answers true if the receiver has a message defined.
     *
     * @return boolean
     */
    public function hasMessage()
    {
        return (boolean) $this->getMessage();
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

    /**
     * Defines the value of the validator attribute.
     *
     * @param Validator $validator
     *
     * @return $this
     */
    public function setValidator(Validator $validator)
    {
        $this->validator = $validator;

        $validator->configureRule($this);

        return $this;
    }

    /**
     * Answers the value of the validator attribute.
     *
     * @return Validator
     */
    public function getValidator()
    {
        return $this->validator;
    }

    /**
     * Answers the validator attributes for the associated form field.
     *
     * @return array
     */
    public function getAttributes()
    {
        $attributes = [];

        $this->extend('updateAttributes', $attributes);

        return $attributes;
    }

    /**
     * Answers true if the rule is valid for use with the validator.
     *
     * @return boolean
     */
    public function isValid()
    {
        return (boolean) $this->getAttribute();
    }

    /**
     * Replaces identified tokens within the given string.
     *
     * @param string $string
     *
     * @return string
     */
    public function replaceTokens($string)
    {
        foreach ($this->getTokenNames($string) as $token) {
            $string = str_replace("{{$token}}", $this->getTokenValue($token), $string);
        }

        return $string;
    }

    /**
     * Answers the validator backend.
     *
     * @return Backend
     */
    protected function backend()
    {
        return $this->validator->backend;
    }

    /**
     * Answers the appropriate validator attribute name for the given mapping name and arguments.
     *
     * @param string $name
     * @param array $args
     *
     * @return string
     */
    protected function attr($name, $args = [])
    {
        return $this->backend()->attr($name, $args);
    }

    /**
     * Answers an array of the token names found within the given string.
     *
     * @param string $string
     *
     * @return array
     */
    protected function getTokenNames($string)
    {
        preg_match_all('/{\K[^}]*(?=})/m', $string, $matches);

        return $matches[0];
    }

    /**
     * Answers the value of the given token name.
     *
     * @param string $name
     *
     * @return mixed
     */
    protected function getTokenValue($name)
    {
        $method = "get{$name}";

        if ($this->hasMethod($method)) {
            return $this->{$method}();
        }

        return $this->$name;
    }
}
