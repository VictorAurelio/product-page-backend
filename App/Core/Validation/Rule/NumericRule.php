<?php

namespace App\Core\Validation\Rule;

class NumericRule implements Rule
{
    public function validate(array $data, string $field, array $params)
    {
        if (empty($data[$field])) {
            return true;
        }

        return is_numeric($data[$field]);
    }

    public function getMessage(array $data, string $field, array $params)
    {
        return "{$field} should be numeric";
    }
}