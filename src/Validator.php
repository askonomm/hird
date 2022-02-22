<?php

declare(strict_types=1);

namespace Askonomm\Validator;

/**
 * The Validator takes in an array of fields, an array of  
 * rules and optionally an array of validators. If no validators 
 * are provided, default validators will be used instead, which are:
 * 
 * - `Validators::len()`
 * - `Validators::email()`
 * - `Validators::required()`
 * 
 * The key of each item in the `$fields` array must correspond to the 
 * the key of each item in the `$rules` array, so that Validator 
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
 * Example usage of Validator: 
 * 
 * ```php
 * $fields = ['email' => 'asko@bien.ee'];
 * $rules = ['email' => 'required|email'];
 * $validator = new Validator($fields, $rules);
 * 
 * if ($validator->fails()) {
 *  return $validator->errors();
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
 *      // added to the array of errors used by Validator.
 *  }
 * ];
 * 
 * // Add validator to Validator
 * $validator = new Validator($fields, $rules, [
 *  'rule-name' => $validator
 * ]);
 * ```
 * 
 * If you want to also use the default validators, and add yours as an extra, 
 * simply join the array of your validators with the array that you get from 
 * `$validator->defaultValidators()`, for example:
 * 
 * ```php
 * $validators = [
 *  ...$this->defaultValidators(),
 *  'rule-name' => $validator,
 * ]);
 * 
 * $validator = new Validator($fields, $rules, $validators);
 * ```
 * @author Asko Nomm <asko@bien.ee>
 */
class Validator
{
    private array $errors = [];

    public function __construct(
        private array $fields,
        private array $rules,
        array $validators = [],
    ) {
        if (empty($validators)) {
            $this->validators = $this->defaultValidators();
        }

        $this->validate();
    }

    /**
     * Returns the default, built-in validators.
     *
     * @return array
     */
    public function defaultValidators(): array
    {
        return [
            'len' => Validators::len(),
            'email' => Validators::email(),
            'required' => Validators::required(),
        ];
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

                    if (!$this->rules[$name]['validates']($value, $modifier)) {
                        $this->errors[] = $this->rules[$name]['error']($field, $modifier);
                    }
                } else {

                    if (!$this->rules[$item]['validates']($value)) {
                        $this->errors[] = $this->rules[$item]['error']($field);
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
