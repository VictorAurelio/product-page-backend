<?php

/**
 * This file is part of my Product Page project.
 *
 * @category  Validation rule
 * @package   App\Core\Validation\Rule
 * @author    Victor Aurélio Rodrigues Ribeiro <victoraurelio_198@hotmail.com>
 * @copyright (c) 2023 Victor Aurelio
 * @link      https://github.com/VictorAurelio/product-page
 */

namespace App\Core\Validation\Rule;

/**
 * This rule checks if a field is required based on the value of another field.
 */
class RequiredIfRule implements Rule
{
    /**
     * Checks if the given field is required based on the value of another field
     * 
     * @param array $data
     * @param string $field
     * @param array $params
     * 
     * @return bool
     */
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
    /**
     * Gets the error message for the rule
     * 
     * @param array $data
     * @param string $field
     * @param array $params
     * 
     * @return string
     */
    public function getMessage(array $data, string $field, array $params)
    {
        $otherField = $params[0];
        $requiredValue = $params[1];

        return json_encode(
            [
                "message" => "{$field} is required if
                                {$otherField} is {$requiredValue}"
            ]
        );
    }
}
