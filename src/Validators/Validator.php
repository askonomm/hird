<?php

namespace Askonomm\Hird\Validators;

interface Validator
{
    public static function validate(string $field, mixed $value, mixed $modifier = null): bool;
    public static function composeError(string $field, mixed $modifier = null): string;
}
