<?php

namespace Askonomm\Validator;

/**
 * Undocumented class
 */
class Validators
{
    /**
     * Undocumented function
     *
     * @return array
     */
    public static function len(): array
    {
        return [
            'error' => function (string $field, mixed $modifier = 0): string {
                return "{$field} is shorter than the required ${modifier} characters.";
            },
            'validates' => function (string $value, mixed $modifier = 0): bool {
                if (!isset($value) || strlen($value) < (int) $modifier) {
                    return false;
                }

                return true;
            }
        ];
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public static function email(): array
    {
        return [
            'error' => function (string $field): string {
                return "${field} is not a valid e-mail address.";
            },
            'validates' => function (string $value): bool {
                if (!isset($value) || !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    return false;
                }

                return true;
            }
        ];
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public static function required(): array
    {
        return [
            'error' => function (string $field): string {
                return "${field} is required.";
            },
            'validates' => function (string $value): bool {
                return isset($value) && $value !== '';
            }
        ];
    }
}
