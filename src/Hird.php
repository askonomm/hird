<?php

declare(strict_types=1);

namespace Asko\Hird;

use Asko\Hird\Validators\DateFormatValidator;
use Asko\Hird\Validators\Validator;
use Asko\Hird\Validators\LenValidator;
use Asko\Hird\Validators\EmailValidator;
use Asko\Hird\Validators\RequiredValidator;

/**
 * Hird takes in an array of `$fields` and an array of  
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
 * Example usage of Hird: 
 * 
 * ```php
 * $fields = ['email' => 'asko@bien.ee'];
 * $rules = ['email' => 'required|email'];
 * $hird = new Hird($fields, $rules);
 * 
 * if ($hird->fails()) {
 *  return $hird->errors();
 * }
 * ```
 * 
 * @author Asko Nomm <asko@asko.dev>
 */
class Hird
{
    private array $errors = [];
    private array $validators = [];
    private array $fieldNames = [];

    public function __construct(
        private array $fields,
        private array $rules,
    ) {
        $this->setFieldNames([]);
        $this->registerDefaultValidators();
    }

    /**
     * Registers the default, built-in validators.
     *
     * @return void
     */
    private function registerDefaultValidators(): void
    {
        $this->registerValidator('len', LenValidator::class);
        $this->registerValidator('email', EmailValidator::class);
        $this->registerValidator('required', RequiredValidator::class);
        $this->registerValidator('date-format', DateFormatValidator::class);
    }

    /**
     * Composes the field names array.
     *
     * @param array $fieldNames
     * @return void
     */
    public function setFieldNames(array $fieldNames): void
    {
        foreach ($this->fields as $field => $value) {
            $this->fieldNames[$field] = $fieldNames[$field] ?? $field;
        }

        // Re-register the default validators to pass the field names.
        $this->registerDefaultValidators();
    }

    /**
     * Registers a validator to a `$ruleName`.
     *
     * @param string $ruleName
     * @param Validator $validator
     * @return void
     */
    public function registerValidator(string $ruleName, string $validator): void
    {
        $class = new \ReflectionClass($validator);
        $instance = null;

        if ($class->getConstructor() !== null) {
            $instance = $class->newInstanceArgs([$this->fields, $this->fieldNames]);
        } else {
            $instance = $class->newInstance();
        }

        $this->validators[$ruleName] = $instance;
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
     * Manually adds an error.
     *
     * @param string $error
     * @return void
     */
    public function addError(string $error): void
    {
        $this->errors[] = $error;
    }

    /**
     * Runs `$this->rules` over `$this->fields` to construct 
     * potential errors that will be stored as an array of strings 
     * in `$this->errors`.
     *
     * @return void
     */
    public function validate(): void
    {
        foreach ($this->rules as $field => $rule) {
            $value = isset($this->fields[$field]) ? $this->fields[$field] : '';

            foreach (explode('|', $rule) as $item) {
                if (str_contains($item, ':')) {
                    $itemParts = explode(':', $item);
                    $name = $itemParts[0];
                    $modifier = implode(':', array_slice($itemParts, 1, count($itemParts) - 1, true));

                    if (!$this->validators[$name]->validate($field, $value, $modifier)) {
                        $this->errors[] = $this->validators[$name]->composeError($field, $modifier);
                    }
                } else {
                    if (!$this->validators[$item]->validate($field, $value)) {
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
        $this->validate();

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
