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
use InvalidArgumentException;

/**
 * A class representing a validation rule to check if a given
 * string has a minimum length. Made for password verification at first
 * but it's useful anywhere.
 */
class MinRule implements Rule
{
    /**
     * Validates if a given field has a minimum length
     * 
     * @param array $data
     * @param string $field
     * @param array $params
     * 
     * @throws InvalidArgumentException
     * 
     * @return bool
     */
    public function validate(array $data, string $field, array $params)
    {
        if (empty($data[$field])) {
            return true;
        }

        if (empty($params[0])) {
            throw new InvalidArgumentException(
                'specify a min length'
            );
        }

        $length = (int) $params[0];

        return strlen($data[$field]) >= $length;
    }
    /**
     * Returns the error message for when the validation fails
     * 
     * @param array $data
     * @param string $field
     * @param array $params
     * 
     * @return string
     */
    public function getMessage(array $data, string $field, array $params)
    {
        $length = (int) $params[0];

        return json_encode(
            [
                "message" => "{$field} should be at least {$length} characters"
            ]
        );
    }
}
