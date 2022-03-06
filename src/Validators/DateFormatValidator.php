<?php

namespace Askonomm\Hird\Validators;

/**
 * Implements a DateFormat validator that has a job 
 * of validating that a given date is in the correct
 * format.
 * 
 * @author Asko Nomm <asko@bien.ee>
 */
class DateFormatValidator implements Validator
{
    /**
     * Returns a boolean `true` when given `$value` is a valid e-mail
     * address. Returns `false` otherwise.
     *
     * @param string $field
     * @param mixed $value
     * @param mixed $modifier
     * @return boolean
     */
    public static function validate(string $field, mixed $value, mixed $modifier = null): bool
    {
        if ($value) {
            $datetime = \DateTime::createFromFormat($modifier, $value);

            return $datetime !== false && !array_sum($datetime::getLastErrors());
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
        return "${field} does not match the required date format ${modifier}.";
    }
}
