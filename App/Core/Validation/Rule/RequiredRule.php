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
 * The RequiredRule class implements the Rule interface and provides methods
 * to validate whether a given field in an array is not empty
 */
class RequiredRule implements Rule
{
    /**
     * checks if the field is not empty and returns a boolean
     * value indicating whether it is valid or not.
     * 
     * @param array $data
     * @param string $field
     * @param array $params
     * 
     * @return bool
     */
    public function validate(array $data, string $field, array $params)
    {
        return !empty($data[$field]);
    }

    /**
     * returns a message indicating that the field is required
     * if the validate() method fails.
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
                "message" => "{$field} is required"
            ]
        );
    }
}
