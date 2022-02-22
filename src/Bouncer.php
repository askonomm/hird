<?php

declare(strict_types=1);

namespace Askonomm\Bouncer;

use Askonomm\Bouncer\Validators\Validator;
use Askonomm\Bouncer\Validators\LenValidator;
use Askonomm\Bouncer\Validators\EmailValidator;
use Askonomm\Bouncer\Validators\RequiredValidator;

/**
 * The Bouncer takes in an array of `$fields` and an array of  
 * `$rules`.
 * 
 * The key of each item in the `$fields` array must correspond to the 
 * the key of each item in the `$rules` array, so that Bouncer 
 * would know how to connect the two to each other.
 * 
 * The `$rules` must have a value that is a string where the rules
 * are separated by a `|` character, and each rule must match the key of
 * the implemented validator, such as `len`, `email` or one that you have 
 * implemented yourself. Additionally, each rule can take in a modifier, 
 * where the name of the rule and the modifier is separated by a `:` character.
 * 
 * For example, say we have a validator called `len` which takes a modifier that
 * lets that validator validate the length of a string, in such a case we'd write
 * that rule as `len:8`, which would indicate using a `len` validator and passing 
 * a modifier with the value `8` to it. 
 * 
 * Example usage of Bouncer: 
 * 
 * ```php
 * $fields = ['email' => 'asko@bien.ee'];
 * $rules = ['email' => 'required|email'];
 * $bouncer = new Bouncer($fields, $rules);
 * 
 * if ($bouncer->fails()) {
 *  return $bouncer->errors();
 * }
 * ```
 * 
 * If you want to implement your own validators then simply create 
 * a data structure that looks like this:
 * 
 * ```php
 * // Create the validator
 * $validator = [
 *  'error' => function(string $field, $modifier): string {
 *      return "${field} had some sort of an error.";
 *  },
 *  'validates' => function(string $value, $modifier): bool {
 *      // validate your $value here and return true if 
 *      // the validation succeeded, or false if there was
 *      // an error, in which case the rule's error will be
 *      // added to the array of errors used by Bouncer.
 *  }
 * ];
 * 
 * // Add validator to Bouncer
 * $bouncer = new Bouncer($fields, $rules, [
 *  'rule-name' => $validator
 * ]);
 * ```
 * 
 * If you want to also use the default validators, and add yours as an extra, 
 * simply join the array of your validators with the array that you get from 
 * `$bouncer->defaultValidators()`, for example:
 * 
 * ```php
 * $validators = [
 *  ...$this->defaultValidators(),
 *  'rule-name' => $validator,
 * ]);
 * 
 * $bouncer = new Bouncer($fields, $rules, $validators);
 * ```
 * 
 * Additionally, you can register your own validators via the 
 * `$bouncer->registerValidator` function like this:
 * 
 * ```php
 * $bouncer->registerValidator('rule-name', $validator]);
 * ```
 * 
 * @author Asko Nomm <asko@bien.ee>
 */
class Bouncer
{
    private array $errors = [];
    private array $validators = [];

    public function __construct(
        private array $fields,
        private array $rules,
    ) {
        $this->registerDefaultValidators();
        $this->validate();
    }

    /**
     * Registers the default, built-in validators.
     *
     * @return array
     */
    public function registerDefaultValidators(): void
    {
        $this->registerValidator('len', (new LenValidator));
        $this->registerValidator('email', (new EmailValidator));
        $this->registerValidator('required', (new RequiredValidator));
    }

    /**
     * Registers a validator to a `$ruleName`.
     *
     * @param string $ruleName
     * @param Validator $validator
     * @return void
     */
    public function registerValidator(string $ruleName, Validator $validator): void
    {
        $this->validators[$ruleName] = $validator;
    }

    /**
     * Removes a validator assigned to the `$ruleName`.
     *
     * @param string $ruleName
     * @return void
     */
    public function removeValidator(string $ruleName): void
    {
        unset($this->validators[$ruleName]);
    }

    /**
     * Runs `$this->rules` over `$this->fields` to construct 
     * potential errors that will be stored as an array of strings 
     * in `$this->errors`.
     *
     * @return void
     */
    private function validate(): void
    {
        foreach ($this->rules as $field => $rule) {
            $value = $this->fields[$field];

            foreach (explode('|', $rule) as $item) {
                if (str_contains($item, ':')) {
                    [$name, $modifier] = explode(':', $item);

                    if (!$this->validators[$name]->validate($value, $modifier)) {
                        $this->errors[] = $this->validators[$name]->composeError($field, $modifier);
                    }
                } else {
                    if (!$this->validators[$item]->validate($value)) {
                        $this->errors[] = $this->validators[$item]->composeError($field);
                    }
                }
            }
        }
    }

    /**
     * Returns a boolean `true` if there have been any errors.
     * Returns `false` otherwise.
     *
     * @return boolean
     */
    public function fails(): bool
    {
        return count($this->errors) !== 0;
    }

    /**
     * Returns an array of strings where each string 
     * is a single error that happened during validation.
     * 
     * @return array
     */
    public function errors(): array
    {
        return $this->errors;
    }

    /**
     * If errors are present, returns the first one.
     * Otherwise returns an empty string.
     *
     * @return string
     */
    public function firstError(): string
    {
        if (count($this->errors) > 0) {
            return $this->errors[0];
        }

        return '';
    }
}
