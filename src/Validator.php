<?php

declare(strict_types=1);

namespace Askonomm\Validator;

/**
 * The Validator takes in an array of fields, an array of  
 * validators and optionally an array of rules. If no rules 
 * are provided, default rules will be used instead, which are:
 * 
 * - `ValidatorRules::len()`
 * - `ValidatorRules::email()`
 * - `ValidatorRules::required()`
 * 
 * The key of each item in the `$fields` array must correspond to the 
 * the key of each item in the `$validators` array, so that Validator 
 * would know how to connect the two to each other.
 * 
 * The `$validators` must have a value that is a string where the rules
 * are separated by a `|` character, and each rule must match the key of
 * the rule. Additionally, each rule can take in a modifier, where the name
 * of the rule and the modifier is separated by a `:` character.
 * 
 * For example, say we have a rule called `len` which takes a modifier that
 * lets that rule validate the length of a string, in such a case we'd write
 * that rule as `len:8`, which would indicate using a `len` rule and passing 
 * a modifier with the value `8` to it. 
 * 
 * Example usage of Validator: 
 * 
 * ```php
 * $fields = ['email' => 'asko@bien.ee'];
 * $validators = ['email' => 'required|email'];
 * $validator = new Validator($fields, $validators);
 * 
 * if ($validator->fails()) {
 *  return $validator->errors();
 * }
 * ```
 * 
 * @author Asko Nomm <asko@bien.ee>
 */
class Validator
{
    private array $errors = [];

    public function __construct(
        private array $fields,
        private array $validators,
        array $rules = [],
    ) {
        if (empty($rules)) {
            $this->rules = $this->defaultRules();
        }

        $this->validate();
    }

    /**
     * Returns the default, built-in validation rules.
     *
     * @return array
     */
    public function defaultRules(): array
    {
        return [
            'len' => ValidatorRules::len(),
            'email' => ValidatorRules::email(),
            'required' => ValidatorRules::required(),
        ];
    }

    /**
     * Runs `$this->validators` over `$this->fields` to construct 
     * potential errors that will be stored as an array of strings 
     * in `$this->errors`.
     *
     * @return void
     */
    private function validate(): void
    {
        foreach ($this->validators as $field => $validator) {
            $value = $this->fields[$field];

            foreach (explode('|', $validator) as $item) {
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
