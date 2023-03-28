<?php

namespace App\Core\Validation\Rule;

class RequiredIfRule implements Rule
{
    public function validate(array $data, string $field, array $params)
    {
        // The first parameter is the name of the field to check against
        // The second parameter is the required value of the other field
        $otherField = $params[0];
        $requiredValue = $params[1];

        if (isset($data[$otherField]) && $data[$otherField] === $requiredValue) {
            return !empty($data[$field]);
        }

        return true;
    }

    public function getMessage(array $data, string $field, array $params)
    {
        $otherField = $params[0];
        $requiredValue = $params[1];
        return "{$field} is required if {$otherField} is {$requiredValue}";
    }
}
