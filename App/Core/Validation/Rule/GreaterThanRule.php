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

use InvalidArgumentException;

/**
 * This class implements the Rule interface and defines a validation rule
 * for checking if a value is greater than a specified number.
 */
class GreaterThanRule implements Rule
{
    /**
     * This method validates if a field's value is greater than a specified number.
     * It throws an InvalidArgumentException if the specified number is missing.
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
        if (!isset($data[$field])) {
            return true;
        }

        if (empty($params[0])) {
            throw new InvalidArgumentException(
                'specify a number to be greater than'
            );
        }

        $comparison = (float) $params[0];

        return (float) $data[$field] > $comparison;
    }
    /**
     * This method returns a message indicating that the field's value
     * should be greater than a specified number.
     * 
     * @param array $data
     * @param string $field
     * @param array $params
     * 
     * @return bool|string
     */
    public function getMessage(array $data, string $field, array $params)
    {
        $comparison = (float) $params[0];

        return json_encode(
            [
                "message" => "{$field} should be greater than {$comparison}"
            ]
        );
    }
}
