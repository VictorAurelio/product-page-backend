<?php

namespace App\Core\Validation\Rule;

/**
 * Validation created to avoid inserts of null/negative values in numeric fields
 */
class NotNullOrNegativeRule implements Rule
{
    /**
     * Validates if the given field value is not null or negative
     * 
     * @param array $data
     * @param string $field
     * @param array $params
     * 
     * @return bool
     */
    public function validate(array $data, string $field, array $params)
    {
        if (!isset($data[$field])) {
            return false;
        }

        return (float) $data[$field] >= 0;
    }
    /**
     * Returns the error message if the field value is null or negative
     * 
     * @param array $data
     * @param string $field
     * @param array $params
     * 
     * @return bool|string
     */
    public function getMessage(array $data, string $field, array $params)
    {
        return json_encode(["message" => "{$field} should not be null or negative"]);
    }
}