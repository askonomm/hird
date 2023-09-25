<?php

declare(strict_types=1);

namespace Asko\Hird\Validators;

/**
 * Implements an e-mail validator that has a job 
 * of validating e-mail addresses.
 * 
 * @author Asko Nomm <asko@asko.dev>
 */
class EmailValidator implements Validator
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
     * @param mixed $value
     * @param mixed $modifier
     * @return boolean
     */
    public function validate(string $field, mixed $value, mixed $modifier = null): bool
    {
        return !!filter_var($value, FILTER_VALIDATE_EMAIL);
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
        return "{$this->fieldNames[$field]} is not a valid e-mail address.";
    }
}
