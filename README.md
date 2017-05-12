# SilverWare Validator

[![Latest Stable Version](https://poser.pugx.org/silverware/validator/v/stable)](https://packagist.org/packages/silverware/validator)
[![Latest Unstable Version](https://poser.pugx.org/silverware/validator/v/unstable)](https://packagist.org/packages/silverware/validator)
[![License](https://poser.pugx.org/silverware/validator/license)](https://packagist.org/packages/silverware/validator)

A form validation module for [SilverStripe v4][silverstripe-framework] which supports alternative
client-side backends and customisable validation rules. Ships with [Parsley.js][parsleyjs] as the
default validation backend.  Configured out-of-the-box for use with [Bootstrap v4][bootstrap] forms.

## Contents

- [Requirements](#requirements)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [Rules](#rules)
- [Issues](#issues)
- [To-Do](#to-do)
- [Contribution](#contribution)
- [Attribution](#attribution)
- [Maintainers](#maintainers)
- [License](#license)

## Requirements

- [SilverStripe Framework v4][silverstripe-framework]
- [Bootstrap v4][bootstrap] (to use with default configuration)

## Installation

Installation is via [Composer][composer]:

```
$ composer require silverware/validator
```

## Configuration

As with all SilverStripe modules, configuration is via YAML. The SilverStripe dependency injector is
used to configure the validator backend. The module ships with a backend that uses [Parsley.js][parsleyjs]:

```yaml
SilverStripe\Core\Injector\Injector:
  ValidatorBackend:
    class: SilverWare\Validator\Backends\ParsleyBackend
```

The module has been designed to be as "backend agnostic" as possible. While Parsley.js is very powerful and
should cater for most validation needs, you could certainly roll your own backend implementation and integrate
it into the validator frontend using configuration.

## Usage

To make use of the validator in your app, simply `use` the validator class in the header of your code:

```php
use SilverWare\Validator\Validator;
```

You will also need to `use` the validator rule classes you wish to apply to your form,
for example:

```php
use SilverWare\Validator\Rules\LengthRule;
use SilverWare\Validator\Rules\PatternRule;
use SilverWare\Validator\Rules\RequiredRule;
```

Create a validator instance in your controller method that returns the `Form`,
for example:

```php
public function Form()
{
    $fields  = FieldList::create([
        ...
    ]);
    
    $actions = FieldList::create([
        ...
    ]);
    
    $validator = Validator::create();
    
    // define validator rules here!
    
    $form = Form::create($this, 'Form', $fields, $actions, $validator);
    
    return $form;
}
```

Now you can define your validator rules using the methods shown below.

### Validator Rules

SilverWare Validator ships with many pre-built rules which you can use
straight away to validate your forms. These rules support both client-side
and server-side validation. Included with SilverWare Validator are the
following rule classes:

- [`AlphaNumRule`](#alphanumrule)
- [`CheckRule`](#checkrule)
- [`ComparisonRule`](#comparisonrule)
- [`DateRule`](#daterule)
- [`DigitsRule`](#digitsrule)
- [`DomainRule`](#domainrule)
- [`EmailRule`](#emailrule)
- [`EqualToRule`](#equaltorule)
- [`IntegerRule`](#integerrule)
- [`LengthRule`](#lengthrule)
- [`MaxCheckRule`](#maxcheckrule)
- [`MaxLengthRule`](#maxlengthrule)
- [`MaxRule`](#maxrule)
- [`MaxWordsRule`](#maxwordsrule)
- [`MinCheckRule`](#mincheckrule)
- [`MinLengthRule`](#minlengthrule)
- [`MinRule`](#minrule)
- [`MinWordsRule`](#minwordsrule)
- [`NotEqualToRule`](#notequaltorule)
- [`NumberRule`](#numberrule)
- [`PatternRule`](#patternrule)
- [`RangeRule`](#rangerule)
- [`RemoteRule`](#remoterule)
- [`RequiredRule`](#requiredrule)
- [`URLRule`](#urlrule)
- [`WordsRule`](#wordsrule)

Each of these rules will be covered in greater detail below.

### Setting Rules

To set a rule for a particular form field, use the `setRule` method,
and pass the field name and the rule instance. For example:

```php
$validator->setRule(
    'MyFieldName',
    LengthRule::create(10, 20) // this field's value must be 10-20 characters in length
);
```

To set more than one rule for a field, you can use the `setRules` method and pass an
array of rule instances:

```php
$validator->setRules(
    'MyDomainName',
    [
        RequiredRule::create(),  // this field is required
        DomainRule::create()     // this field must be a valid domain name
    ]
);
```

### Required Fields

Since fields are often required for almost any form, shortcut methods
have been provided to define required fields. Use `addRequiredField` with the
field name and optional custom message parameter:

```php
$validator->addRequiredField('MyRequiredField');
$validator->addRequiredField('MyOtherRequiredField', 'Dis field be mandatory, yo!');
```

To add required fields in bulk, use `addRequiredFields` with an
array of field names:

```php
$validator->addRequiredFields([
    'ThisFieldIsRequired',
    'SoIsThisField',
    'AndThisField'
]);
```

### Custom Messages

All rule classes come with a default message which is shown to the
user when the rule validation fails.  You can define your own custom
messages by using the `setMessage` method on a rule, for example:

```php
$validator->setRule(
    'MyAge',
    MaxRule::create(130)->setMessage(
        'I find it hard to believe you are over 130 years old!'
    )
)
```

### Disabling Validation

By default, SilverWare Validator performs both client-side and
server-side validation. While it's recommended to leave both
methods of validation enabled, if you need to disable either, you can
use the following methods:

```php
$validator->setClientSide(false);  // disables client-side validation
$validator->setServerSide(false);  // disables server-side validation
```

### Trigger Events

The out-of-the-box validator [Parsley.js][parsleyjs] is set to trigger form validation
upon field change by default. You can customise when you'd like validation to
be triggered by using the `setTriggerOn` method on the backend, for example:

```php
$validator->getBackend()->setTriggerOn([
    'focusin',
    'focusout'
]);
```

You can also simply pass the trigger events as a string:

```php
$validator->getBackend()->setTriggerOn('focusin focusout');
```

Parsley.js supports triggering by any of the standard [jQuery events](http://api.jquery.com/category/events).

## Rules

### AlphaNumRule

```php
use SilverWare\Validator\Rules\AlphaNumRule;
```

Tests that the value is an alphanumeric string, containing only basic letters, numbers,
and the underscore character.

```php
$validator->setRule(
    'MyFieldName',
    AlphaNumRule::create()
);
```

### CheckRule

```php
use SilverWare\Validator\Rules\CheckRule;
```

Used for a `OptionsetField` such as `CheckboxSetField`. Ensures that
the user chooses a certain number of options, between the given
`$min` and `$max` parameters.

```php
$validator->setRule(
    'MyCheckboxSet',
    CheckRule::create(2, 4)  // user must select between 2 and 4 options
);
```

### ComparisonRule

```php
use SilverWare\Validator\Rules\ComparisonRule;
```

Compares the value of one field to another, using the specified comparison type:

- `ComparisonRule::LESS_THAN `
- `ComparisonRule::LESS_THAN_OR_EQUAL`
- `ComparisonRule::GREATER_THAN`
- `ComparisonRule::GREATER_THAN_OR_EQUAL`

```php
$validator->setRule(
    'MyLesserValue',
    ComparisonRule::create(
        ComparisonRule::GREATER_THAN,
        'MyGreaterValue'
    )
);
```

### DateRule

```php
use SilverWare\Validator\Rules\DateRule;
```

Ensures that the entered value is a date of the specified format.
The rule constructor supports two arguments. The first is the
required date format using the client-side [Moment.js][momentjs] specification.

The optional second argument specifies the server-side PHP date format,
however the rule will convert the client-side format appropriately if
you omit the second argument.

```php
$validator->setRule(
    'MyDateField',
    DateRule::create('YYYY-MM-DD')  // ensures an ISO standard date, e.g. 2017-05-12
);
```

### DigitsRule

```php
use SilverWare\Validator\Rules\DigitsRule;
```

Ensures only digits are entered, i.e. the numbers 0-9 only.

```php
$validator->setRule(
    'MyDigitsField',
    DigitsRule::create()
);
```

### DomainRule

```php
use SilverWare\Validator\Rules\DomainRule;
```

Tests that the entered value is a valid domain name. Supports the
new TLD domains and also `localhost`.

```php
$validator->setRule(
    'MyDomainName',
    DomainRule::create()
);
```

### EmailRule

```php
use SilverWare\Validator\Rules\EmailRule;
```

Ensures that the value is a valid email address.

```php
$validator->setRule(
    'MyEmailField',
    EmailRule::create()
);
```

### EqualToRule

```php
use SilverWare\Validator\Rules\EqualToRule;
```

Ensures that the value of one field is equal to the value of another.

```php
$validator->setRule(
    'MyFirstValue',
    EqualToRule::create('MySecondValue')
);
```

### IntegerRule

```php
use SilverWare\Validator\Rules\IntegerRule;
```

Tests that the entered value is a valid positive or negative integer.

```php
$validator->setRule(
    'MyIntField',
    IntegerRule::create()
);
```

### LengthRule

```php
use SilverWare\Validator\Rules\LengthRule;
```

Ensures that the length of the entered value is between the specified `$min` and `$max` range.

```php
$validator->setRule(
    'MyFieldName',
    LengthRule::create(20, 40)  // value must be between 20-40 characters in length
);
```

### MaxCheckRule

```php
use SilverWare\Validator\Rules\MaxCheckRule;
```

Used for a `OptionsetField` such as `CheckboxSetField`. Specifies that
the user may only select a maximum number of options.

```php
$validator->setRule(
    'MyCheckboxSet',
    MaxCheckRule::create(4)  // user can select a maximum of 4 options
);
```

### MaxLengthRule

```php
use SilverWare\Validator\Rules\MaxLengthRule;
```

Ensures that the entered value is of a maximum length.

```php
$validator->setRule(
    'MyFieldName',
    MaxLengthRule::create(40)  // value must be 40 characters in length or less
);
```

### MaxRule

```php
use SilverWare\Validator\Rules\MaxRule;
```

Tests that a numeric value is less than or equal to the specified amount.

```php
$validator->setRule(
    'MyNumberField',
    MaxRule::create(255)  // number value must be 255 or less
);
```

### MaxWordsRule

```php
use SilverWare\Validator\Rules\MaxWordsRule;
```

Ensures that the value is of the specified number of words or less.

```php
$validator->setRule(
    'MyTextArea',
    MaxWordsRule::create(50) // value must be 50 words or less
);
```

### MinCheckRule

```php
use SilverWare\Validator\Rules\MinCheckRule;
```

Used for a `OptionsetField` such as `CheckboxSetField`. Ensures that
the user selects a minimum number of options.

```php
$validator->setRule(
    'MyCheckboxSet',
    MinCheckRule::create(2)  // user must select at least 2 options
);
```

### MinLengthRule

```php
use SilverWare\Validator\Rules\MinLengthRule;
```

Ensures that the entered value is of a minimum length.

```php
$validator->setRule(
    'MyFieldName',
    MinLengthRule::create(20)  // value must be at least 20 characters in length
);
```

### MinRule

```php
use SilverWare\Validator\Rules\MinRule;
```

Tests that the numeric value is greater than or equal to the specified amount.

```php
$validator->setRule(
    'MyNumberField',
    MinRule::create(42)  // the entered value must be 42 or greater
);
```

### MinWordsRule

```php
use SilverWare\Validator\Rules\MinWordsRule;
```

Ensures that the entered value is of the specified number of words or greater.

```php
$validator->setRule(
    'MyTextArea',
    MinWordsRule::create(20)  // value must contain at least 20 words
);
```

### NotEqualToRule

```php
use SilverWare\Validator\Rules\NotEqualToRule;
```

Ensures that the value of one field is *NOT* equal to the value of another.

```php
$validator->setRule(
    'MyFirstValue',
    NotEqualToRule::create('MySecondValue')
);
```

### NumberRule

```php
use SilverWare\Validator\Rules\NumberRule;
```

Tests that the entered value is numeric, i.e. an integer or a
floating-point number.

```php
$validator->setRule(
    'MyNumericValue',
    NumberRule::create()  // value must be numeric
);
```

### PatternRule

```php
use SilverWare\Validator\Rules\PatternRule;
```

Uses a regular expression to test the entered value. Ensure that the
format of your regular expression works in both JavaScript, and also
PHP `preg_match` so that both client and server validation functions correctly.

```php
$validator->setRule(
    'MyFieldName',
    PatternRule::create('/ware$/')  // ensures the entered string ends with 'ware'
);
```

### RangeRule

```php
use SilverWare\Validator\Rules\RangeRule;
```

Specifies a number range that the entered value must be between.

```php
$validator->setRule(
    'MyNumericValue',
    RangeRule::create(1, 5)  // value must be between 1 and 5
);
```

### RemoteRule

```php
use SilverWare\Validator\Rules\RemoteRule;
```

Tests the value on the client-side using an Ajax request, and uses
[Guzzle][guzzle] to test the value when submitted to the server.

The following arguments are permitted for the rule constructor:

```php
$rule = RemoteRule::create(
    $url,  // url to call
    $params,  // request parameters, e.g. ['myVar' => 123]
    $options, // options for request, e.g. ['type' => 'POST']
    $remoteValidator  // remote validator to use, 'default' or 'reverse'
);
```

For example, you might use the `RemoteRule` to check if a
username is available:

```php
$validator->setRule(
    'MyUserName',
    RemoteRule::create(
        $this->Link('checkusername')  // controller method
    )->setMessage('That username is unavailable')
);
```

The server will receive the name of the field and it's value
as parameters, i.e. `?MyUserName=sallybloggs`.
By default, if the `RemoteRule` receives an HTTP status code of
between 200-299, it is considered to be valid. Anything else will
return an invalid result.

If you'd like to reverse this behavior, i.e. 200-299 is considered
to be invalid, you can use "reverse" mode:

```php
$validator->setRule(
    'MyUserName',
    RemoteRule::create(
        $this->Link('checkusername')
    )->setRemoteValidator('reverse')
);
```

For more information on using remote validation, see the
[Parsley.js documentation](http://parsleyjs.org/doc/index.html#remote).

### RequiredRule

```php
use SilverWare\Validator\Rules\RequiredRule;
```

Defines the specified field as a required (mandatory) field that
must be completed by the user.

```php
$validator->setRule(
    'MyRequiredField',
    RequiredRule::create()
);
```

### URLRule

```php
use SilverWare\Validator\Rules\URLRule;
```

Ensures that the entered value is a valid URL.

```php
$validator->setRule(
    'MyURL',
    URLRule::create()
);
```

### WordsRule

```php
use SilverWare\Validator\Rules\WordsRule;
```

Ensures that the entered value is between `$min` and `$max` words.

```php
$validator->setRule(
    'MyTextArea',
    WordsRule::create(10, 25)  // user must enter between 10 and 25 words
);
```

## Issues

Please use the [GitHub issue tracker][issues] for bug reports and feature requests.

## To-Do

- Tests

## Contribution

Your contributions are gladly welcomed to help make this project better.
Please see [contributing](CONTRIBUTING.md) for more information.

## Attribution

- Makes use of [Parsley.js][parsleyjs] by [Guillaume Potier](https://github.com/guillaumepotier).
- Makes use of [Moment.js][momentjs] by [Iskren Ivov Chernev](https://github.com/ichernev) and others.
- Makes use of [Guzzle][guzzle] by [Michael Dowling](https://github.com/mtdowling) and others.
- Makes use of [Bootstrap][bootstrap] by the
  [Bootstrap Authors](https://github.com/twbs/bootstrap/graphs/contributors)
  and [Twitter, Inc](https://twitter.com).
- Inspired by [SilverStripe ZenValidator][zenvalidator] by [Shea Dawson](https://github.com/sheadawson).

## Maintainers

[![Colin Tucker](https://avatars3.githubusercontent.com/u/1853705?s=144)](https://github.com/colintucker) | [![Praxis Interactive](https://avatars2.githubusercontent.com/u/1782612?s=144)](http://www.praxis.net.au)
---|---
[Colin Tucker](https://github.com/colintucker) | [Praxis Interactive](http://www.praxis.net.au)

## License

[BSD-3-Clause](LICENSE.md) &copy; Praxis Interactive

[silverware]: https://github.com/praxisnetau/silverware
[composer]: https://getcomposer.org
[silverstripe-framework]: https://github.com/silverstripe/silverstripe-framework
[issues]: https://github.com/praxisnetau/silverware-validator/issues
[parsleyjs]: http://parsleyjs.org
[zenvalidator]: https://github.com/sheadawson/silverstripe-zenvalidator
[bootstrap]: https://v4-alpha.getbootstrap.com
[momentjs]: https://momentjs.com
[guzzle]: https://github.com/guzzle/guzzle