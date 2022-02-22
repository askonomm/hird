<?php

namespace Askonomm\Bouncer\Validators;

interface Validator
{
    public static function validate(mixed $value, mixed $modifier = null): bool;
    public static function composeError(string $field, mixed $modifier = null): string;
}
