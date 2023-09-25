# Hird

[![Latest Stable Version](http://poser.pugx.org/asko/hird/v)](https://packagist.org/packages/asko/hird)

> Hirds, also known as housecarls, was a gathering of hirdmen, who functioned as the king's personal guards during the viking age and the early middle ages.

![Hird](https://user-images.githubusercontent.com/84135165/155371599-301b0df9-fa92-4902-a287-9c9950515c0c.jpeg)

Hird is an extensible validation library for your data with sane defaults.

## Installation

You can install the package via composer:

```
composer require asko/hird
```

## Usage

Hird takes in an array of `$fields` and an array of `$rules`.

The key of each item in the `$fields` array must correspond to the the key of each item in the `$rules` array, so that Hird would know how to connect the two to each other.

The `$rules` must have a value that is a string where the rules are separated by a `|` character, and each rule must match the key of the implemented validator, such as `len`, `email` or one that you have implemented yourself. Additionally, each rule can take in a modifier, where the name of the rule and the modifier is separated by a `:` character.

For example, say we have a validator called `len` which takes a modifier that lets that validator validate the length of a string, in such a case we'd write that rule as `len:8`, which would indicate using a `len` validator and passing a modifier with the value `8` to it.

### Example usage

An example usage of Hird looks like this:

```php
use Askonomm\Hird\Hird;

$fields = ['email' => 'asko@asko.dev'];
$fieldNames = ['email' => 'E-mail'];
$rules = ['email' => 'required|email|len:5'];
$hird = new Hird($fields, $rules, $fieldNames);

if ($hird->fails()) {
    return $hird->errors();
}
```

From the above example, you can see that there are two Hird methods being used such as `$hird->fails()` and `$hird->errors()`. The `$hird->fails()` method will run the validation and return a boolean depending on whether the validation failed or not, `true` if it did. The `$hird->errors()` method will return an array of all the errors that occured, as defined by the validators.

You can also get the first error rather than all errors by using the method `$hird->firstError()`.

If you wish to run the validation without needing to call `$hird->fails()`, you can instead call `$hird->validate()`.

Another thing you may notice is the presence of `$fieldNames`, which is a way overwriting the field names for use within the error messages, so that `email` could become `E-mail` when shown to the user. If you don't care about this then you can entirely skip this as only the `$fields` and `$rules` are required for Hird to work.

## Built-in validators

There are a number of built-in validators available for use by default. If you want to remove a built-in validator, you can remove one using the `$hird->removeValidator('rule-name')` method.

### `email`

The `email` validator validates an e-mail address, and it is registered as the `email` rule.

```php
use Askonomm\Hird\Hird;

$fields = ['email' => 'asko@bien.ee'];
$rules = ['email' => 'email'];
$hird = new Hird($fields, $rules);
```

### `len`

The `len` validator validates the length of a string, and it is registered as the `len` rule. The `len` validator also accepts, and requires, a modifier. A modifier can be passed to a rule by appending a color character `:` to it, and passing the modifier after it, like `len:8`.

```php
use Askonomm\Hird\Hird;

$fields = ['password' => 'SuperSecretPassword'];
$rules = ['password' => 'len:10'];
$hird = new Hird($fields, $rules);
```

### `required`

The `required` validator validates the presence of value, and it is registered as the `required` rule. It will pass validation if the value is set and the value is not an empty string.

```php
use Askonomm\Hird\Hird;

$fields = ['password' => 'SuperSecretPassword'];
$rules = ['password' => 'required'];
$hird = new Hird($fields, $rules);
```

### `date-format`

The `date-format` validator validates the string format of a date, and is registered as the `date-format` rule. It will pass validation if the value is set and the value is in the format specified by the rule.

```php
use Askonomm\Hird\Hird;

$fields = ['date' => '2020-09-17'];
$rules = ['date' => 'date-format:Y-m-d'];
$hird = new Hird($fields, $rules);
```

## Creating validators

You can also create your own validators, or replace existing ones if you're not happy with them.

**Note:** To replace an existing one, first remove the built-in validator via `$hird->removeValidator('rule-name')` and then add your own via `$hird->registerValidator('rule-name', ValidatorClass::class)`.

A validator is a class that implements the `Validator` interface. A full example of a correct validator would look something like this:

```php
use Asko\Hird\Validators\Validator;

class EmailValidator implements Validator
{
    public function __construct(
        private array $fields, // all fields data
        private array $fieldNames, // names of fields
    ){
    }

    /**
     * Returns a boolean `true` when given `$value` is a valid e-mail
     * address. Returns `false` otherwise.
     *
     * @param mixed $value
     * @param mixed $modifier
     * @return boolean
     */
    public function validate(string $field, mixed $value, mixed $modifier = null): bool
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL);
    }

    /**
     * Composes the error message in case the validation fails.
     *
     * @param string $field
     * @param mixed $modifier
     * @return string
     */
    public function composeError(string $field, mixed $modifier = null): string
    {
        return "${field} is not a valid e-mail address.";
    }
}
```

You can see that there are two methods, one for validating the `$value` and the other for composing an error message if the validation fails. Both functions take in a `$modifier` argument, which will only have value if the validator is using modifiers. For example, the `len` validator is using modifiers to determine how long of a string should be required, by passing the rule in as `len:{number-of-characters}`.

Once you've created the class for your validator, you can register it by calling `$hird->registerValidator('rule-name', YourValidator::class)`.
