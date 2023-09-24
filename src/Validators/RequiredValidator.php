<?php

declare(strict_types=1);

namespace Asko\Hird\Validators;

/**
 * Implements a length validator that has a job of validating 
 * that a given string is of correct length.
 * 
 * @author Asko Nomm <asko@asko.dev>
 */
class RequiredValidator implements Validator
{
    /**
     * Returns a boolean `true` when given `$value` is present 
     * and not empty. Returns `false` otherwise.
     *
     * @param mixed $value
     * @param mixed $modifier
     * @return boolean
     */
    public function validate(string $field, mixed $value, mixed $modifier = null): bool
    {
        return isset($value) && $value !== '';
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
        return "{$field} is required.";
    }
}
