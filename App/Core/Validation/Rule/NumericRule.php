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
 * This class implements a validation rule to verify if a given value is numeric
 */
class NumericRule implements Rule
{
    /**
     * This method checks if the specified field in the data is numeric
     * 
     * @param array $data
     * @param string $field
     * @param array $params
     * 
     * @return bool
     */
    public function validate(array $data, string $field, array $params)
    {
        if (empty($data[$field])) {
            return true;
        }

        return is_numeric($data[$field]);
    }
    /**
     * This method returns the error message when the validation rule fails
     * 
     * @param array $data
     * @param string $field
     * @param array $params
     * 
     * @return string
     */
    public function getMessage(array $data, string $field, array $params)
    {
        return json_encode(
            [
                "message" => "{$field} should be numeric"
            ]
        );
    }
}