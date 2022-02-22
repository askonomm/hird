<?php

declare(strict_types=1);

namespace Askonomm\Validator;

/**
 * The Validator class takes in an array of fields and 
 * an array of validators. Each field must have a key 
 * that matches the validator key so that `Validator` class
 * would know how to connect the two, and the value of the each 
 * validator is a string that consists of rules separated by the 
 * `|` character.
 * 
 * Example use:
 * 
 * ```php
 * $fields = ['email' => 'test@example.com'];
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
     */
    public function errors(): array
    {
        return $this->errors;
    }

    public function firstError(): string
    {
        if (count($this->errors) > 0) {
            return $this->errors[0];
        }

        return '';
    }
}
