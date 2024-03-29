<?php

declare(strict_types=1);

namespace Asko\Hird\Validators;

/**
 * Implements a DateFormat validator that has a job 
 * of validating that a given date is in the correct
 * format.
 * 
 * @author Asko Nomm <asko@asko.dev>
 */
class DateFormatValidator implements Validator
{
    public function __construct(
        private array $fields,
        private array $fieldNames,
    ) {
    }

    /**
     * Returns a boolean `true` when given `$value` is a valid e-mail
     * address. Returns `false` otherwise.
     *
     * @param string $field
     * @param mixed $value
     * @param mixed $modifier
     * @return boolean
     */
    public function validate(string $field, mixed $value, mixed $modifier = null): bool
    {
        if ($value) {
            $datetime = \DateTime::createFromFormat($modifier, $value);

            return $datetime !== false;
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
        return "{$this->fieldNames[$field]} does not match the required date format {$modifier}.";
    }
}
