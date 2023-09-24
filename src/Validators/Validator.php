<?php

declare(strict_types=1);

namespace Asko\Hird\Validators;

interface Validator
{
    public function validate(string $field, mixed $value, mixed $modifier = null): bool;
    public function composeError(string $field, mixed $modifier = null): string;
}
