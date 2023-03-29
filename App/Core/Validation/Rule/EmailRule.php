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

use App\Core\Validation\Rule\Rule;

/**
 * The EmailRule class is a validation rule that implements the Rule interface.
 * It checks if a given field in an array of data contains a valid email address.
 */
class EmailRule implements Rule
{
    /**
     * The validate method takes in an array of data, a string representing
     * the field to be validated, and an array of parameters. It returns a boolean
     * value indicating whether the field passes the validation rule.
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

        return str_contains($data[$field], '@');
    }

    /**
     * The getMessage method returns a string message that describes the validation
     * rule that was violated if the field fails validation.
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
                "message" => "{$field} should be an email"
            ]
        );
    }
}
