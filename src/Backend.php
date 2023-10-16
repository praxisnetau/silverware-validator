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

use SilverStripe\Core\ClassInfo;
use SilverStripe\Core\Config\Configurable;
use SilverStripe\Core\Extensible;
use SilverStripe\Core\Injector\Injectable;
use SilverStripe\Forms\Form;
use SilverStripe\Forms\FormField;
use SilverStripe\View\Requirements;
use SilverWare\Validator\Validator;

/**
 * An extension of the object class for the abstract parent class of validator backends.
 *
 * @package SilverWare\Validator
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware-validator
 */
abstract class Backend
{
    use Configurable;
    use Extensible;
    use Injectable;

    /**
     * An array of required JavaScript files.
     *
     * @var array
     * @config
     */
    private static $required_js = [];

    /**
     * An array of required CSS files.
     *
     * @var array
     * @config
     */
    private static $required_css = [];

    /**
     * The attribute configuration used for this backend.
     *
     * @var array
     * @config
     */
    private static $attribute = [];

    /**
     * The attribute mappings defined for this backend.
     *
     * @var array
     * @config
     */
    private static $mappings = [];

    /**
     * The validator frontend associated with this backend.
     *
     * @var Validator
     */
    protected $frontend;

    /**
     * Constructs the object upon instantiation.
     */
    public function __construct()
    {

    }

    /**
     * Defines the value of the frontend attribute.
     *
     * @param Validator $frontend
     *
     * @return $this
     */
    public function setFrontend(Validator $frontend)
    {
        $this->frontend = $frontend;

        return $this;
    }

    /**
     * Answers the value of the Frontend attribute.
     *
     * @return Validator
     */
    public function getFrontend()
    {
        return $this->frontend;
    }

    /**
     * Answers an array of the validator classes for the given form.
     *
     * @param Form $form
     *
     * @return array
     */
    public function getClassesForForm(Form $form)
    {
        return [$this->getHTMLClass()];
    }

    /**
     * Answers an array of the validator attributes for the given form.
     *
     * @param Form $form
     *
     * @return array
     */
    public function getAttributesForForm(Form $form)
    {
        return [
            'data-client-side' => ($this->frontend->getClientSide() ? 'true' : 'false')
        ];
    }

    /**
     * Answers an array of the validator attributes for the given form field.
     *
     * @param FormField $field
     *
     * @return array
     */
    public function getAttributesForField(FormField $field)
    {
        return [];
    }

    /**
     * Answers the HTML class name for the receiver.
     *
     * @return string
     */
    public function getHTMLClass()
    {
        return strtolower(ClassInfo::shortName(static::class));
    }

    /**
     * Initialises the validator backend (with extension hooks).
     *
     * @return void
     */
    public function doInit()
    {
        // Trigger Before Init Hook:

        $this->extend('onBeforeInit');

        // Perform Initialisation:

        $this->init();

        // Trigger After Init Hook:

        $this->extend('onAfterInit');
    }

    /**
     * Answers the appropriate validator attribute name for the given mapping name and arguments.
     *
     * @param string $name
     * @param array $args
     *
     * @return string
     */
    public function attr($name, $args = [])
    {
        $attr = $this->hasMapping($name) ? $this->getMapping($name) : $name;

        if (func_num_args() > 1) {
            return $this->prefix(vsprintf($attr, (array) $args));
        }

        return $this->prefix($attr);
    }

    /**
     * Prefixes the given attribute name (if required).
     *
     * @param string $name
     *
     * @return string
     */
    public function prefix($name)
    {
        if ($prefix = $this->config()->attribute['prefix']) {

            if (strpos($name, $prefix) !== 0) {
                return sprintf('%s%s', $prefix, $name);
            }

        }

        return $name;
    }

    /**
     * Answers the attribute mapping with the given name.
     *
     * @param string $name
     *
     * @return string
     */
    public function getMapping($name)
    {
        if ($mappings = $this->getMappings()) {

            if (isset($mappings[$name])) {
                return $mappings[$name];
            }

        }
    }

    /**
     * Answers true if an attribute mapping exists with the given name.
     *
     * @param string $name
     *
     * @return boolean
     */
    public function hasMapping($name)
    {
        return (boolean) $this->getMapping($name);
    }

    /**
     * Answers the configured attribute mappings for the receiver.
     *
     * @return array
     */
    public function getMappings()
    {
        return $this->config()->mappings;
    }

    /**
     * Answers the default attribute name from configuration.
     *
     * @return string
     */
    public function getDefaultAttribute()
    {
        return $this->config()->attribute['default'];
    }

    /**
     * Applies configuration to the provided validator rule.
     *
     * @param Rule $rule
     *
     * @return Rule
     */
    public function configureRule(Rule $rule)
    {
        if ($config = $this->getRuleConfig($rule)) {

            if (isset($config['type'])) {
                $rule->setType($config['type']);
            }

            if (isset($config['format'])) {
                $rule->setFormat($config['format']);
            }

            if (isset($config['attribute'])) {
                $rule->setAttribute($config['attribute']);
            }

        }

        return $rule;
    }

    /**
     * Answers an array of JavaScript files required by the receiver.
     *
     * @return array
     */
    public function getRequiredJS()
    {
        $js = $this->config()->required_js;

        $this->extend('updateRequiredJS', $js);

        return $js;
    }

    /**
     * Answers an array of CSS files required by the receiver.
     *
     * @return array
     */
    public function getRequiredCSS()
    {
        $css = $this->config()->required_css;

        $this->extend('updateRequiredCSS', $css);

        return $css;
    }

    /**
     * Loads the CSS and scripts required by the receiver.
     *
     * @return void
     */
    public function loadRequirements()
    {
        // Load Required CSS:

        foreach ($this->getRequiredCSS() as $css) {
            Requirements::css($css);
        }

        // Load Required JavaScript:

        foreach ($this->getRequiredJS() as $js) {
            Requirements::javascript($js);
        }
    }

    /**
     * Initialises the validator backend.
     *
     * @return void
     */
    protected function init()
    {
        // Load Requirements:

        $this->loadRequirements();
    }

    /**
     * Flattens the given array of attributes.
     *
     * @param array $attributes
     *
     * @return array
     */
    protected function flatten($attributes)
    {
        foreach ($attributes as $name => $value) {

            if (is_array($value)) {
                $attributes[$name] = implode(' ', array_filter($value));
            }

        }

        return $attributes;
    }

    /**
     * Answers the configuration array for the provided rule.
     *
     * @param Rule $rule
     *
     * @return array
     */
    protected function getRuleConfig(Rule $rule)
    {
        if ($rules = $this->config()->rules) {

            if (isset($rules[get_class($rule)])) {
                return $rules[get_class($rule)];
            }

        }

        return [];
    }
}
