<?php

declare(strict_types=1);

namespace Asko\Hird\Validators;

/**
 * Implements a length validator that has a job of validating 
 * that a given string is of correct length.
 * 
 * @author Asko Nomm <asko@asko.dev>
 */
class LenValidator implements Validator
{
    public function __construct(
        private array $fields,
        private array $fieldNames,
    ) {
    }

    /**
     * Returns a boolean `true` when given `$value` is as long as
     * required. Returns `false` otherwise.
     *
     * @param mixed $value
     * @param mixed $modifier
     * @return boolean
     */
    public function validate(string $field, mixed $value, mixed $modifier = null): bool
    {
        // If no modifier present then this validator will always validate.
        if (!$modifier) {
            return true;
        }

        if (!isset($value) || strlen($value) < (int) $modifier) {
            return false;
        }

        return true;
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
        return "{$this->fieldNames[$field]} is shorter than the required {$modifier} characters.";
    }
}
