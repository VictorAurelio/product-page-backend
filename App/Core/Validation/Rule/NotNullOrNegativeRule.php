<?php

namespace App\Core\Validation\Rule;

class NotNullOrNegativeRule implements Rule
{
    public function validate(array $data, string $field, array $params)
    {
        if (!isset($data[$field])) {
            return false;
        }

        return (float) $data[$field] >= 0;
    }

    public function getMessage(array $data, string $field, array $params)
    {
        return json_encode(["message" => "{$field} should not be null or negative"]);
    }
}