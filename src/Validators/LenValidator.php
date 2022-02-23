<?php

namespace Askonomm\Hird\Validators;

/**
 * Implements a length validator that has a job of validating 
 * that a given string is of correct length.
 * 
 * @author Asko Nomm <asko@bien.ee>
 */
class LenValidator implements Validator
{
    /**
     * Returns a boolean `true` when given `$value` is as long as
     * required. Returns `false` otherwise.
     *
     * @param mixed $value
     * @param mixed $modifier
     * @return boolean
     */
    public static function validate(mixed $value, mixed $modifier = null): bool
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
    public static function composeError(string $field, mixed $modifier = null): string
    {
        return "{$field} is shorter than the required ${modifier} characters.";
    }
}
