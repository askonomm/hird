# Bouncer

[![Latest Version on Packagist](https://img.shields.io/packagist/v/askonomm/bouncer.svg?style=flat-square)](https://packagist.org/packages/askonomm/bouncer)

An extensible validation library for your data with sane defaults.

## Installation

You can install the package via composer:

```
composer require askonomm/bouncer
```

## Usage

The Bouncer takes in an array of fields, an array of rules and optionally an array of validators. If no validators are provided, default validators will be used instead, which are:

- `Validators::len()`
- `Validators::email()`
- `Validators::required()`

The key of each item in the `$fields` array must correspond to the the key of each item in the `$rules` array, so that Bouncer would know how to connect the two to each other.

The `$rules` must have a value that is a string where the rules are separated by a `|` character, and each rule must match the key of the implemented validator, such as `len`, `email` or one that you have implemented yourself. Additionally, each rule can take in a modifier, where the name of the rule and the modifier is separated by a `:` character.
 
For example, say we have a validator called `len` which takes a modifier that lets that validator validate the length of a string, in such a case we'd write that rule as `len:8`, which would indicate using a `len` validator and passing a modifier with the value `8` to it. 

### Example usage

An example usage of Bouncer looks like this:

```php
$fields = ['email' => 'asko@bien.ee'];
$rules = ['email' => 'required|email|len:5'];
$bouncer = new Bouncer($fields, $rules);

if ($bouncer->fails()) {
    return $bouncer->errors();
}
```

From the above example, you can see that there are two Bouncer methods being used such as `$bouncer->fails()` and `$bouncer->errors()`. The `$bouncer->fails()` method will return a boolean depending on whether the validation failed or not, `true` if it did. The `$bouncer->errors()` method will return an array of all the errors that occured, as defined by the validators.

You can also get the first error rather than all errors by using the method `$bouncer->firstError()`. 

## Built-in validators

There are a number of built-in validators avaiable for use by default. If you want to remove a built-in validator, you can remove one using the `$bouncer->removeValidator('rule-name')` method.

### `email`

The `email` validator validates an e-mail address, and it is registered as the `email` rule.

```php
$fields = ['email' => 'asko@bien.ee'];
$rules = ['email' => 'email'];
$bouncer = new Bouncer($fields, $rules);
```

### `len`

The `len` validator validates the length of a string, and it is registered as the `len` rule. The `len` validator also accepts, and requires, a modifier. A modifier can be passed to a rule by appending a color character `:` to it, and passing the modifier after it, like `len:8`.

```php
$fields = ['password' => 'SuperSecretPassword'];
$rules = ['password' => 'len:10'];
$bouncer = new Bouncer($fields, $rules);
```

### `required`

The `required` validator validates the presence of value, and it is registered as the `required` rule. It will pass validation if the value is set and the value is not an empty string.

```php
$fields = ['password' => 'SuperSecretPassword'];
$rules = ['password' => 'required'];
$bouncer = new Bouncer($fields, $rules);
```

## Creating validators

You can also create your own validators, or replace existing ones if you're not happy with them. 

**Note:** To replace an existing one, first remove the built-in validator via `$bouncer->removeValidator('rule-name')` and then add your own via `$bouncer->registerValidator('rule-name', $validator)`. 


A validator is a class that implements the `Validator` interface. A full example of a correct validator would look something like this:

```php
class EmailValidator implements Validator
{
    /**
     * Returns a boolean `true` when given `$value` is a valid e-mail
     * address. Returns `false` otherwise.
     *
     * @param mixed $value
     * @param mixed $modifier
     * @return boolean
     */
    public static function validate(mixed $value, mixed $modifier = null): bool
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
    public static function composeError(string $field, mixed $modifier = null): string
    {
        return "${field} is not a valid e-mail address.";
    }
}
```

You can see that there are two methods, one for validating the `$value` and the other for composing an error message if the validation fails. Both functions take in a `$modifier` argument, which will only have value if the validator is using modifiers. For example, the `len` validator is using modifiers to determine how long of a string should be required, by passing the rule in as `len:{number-of-characters}`. 

Once you've created the class for your validator, you can register it by calling `$bouncer->registerValidator('rule-name', (new YourValidatorClass))`. 