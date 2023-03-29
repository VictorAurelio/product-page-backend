<?php

/**
 * This file is part of my Product Page project.
 *
 * @category  Validation rules
 * @package   App\Core\Validation\Rule
 * @author    Victor Aurélio Rodrigues Ribeiro <victoraurelio_198@hotmail.com>
 * @copyright (c) 2023 Victor Aurelio
 * @link      https://github.com/VictorAurelio/product-page
 */

namespace App\Core\Validation\Rule;

use InvalidArgumentException;

/**
 * Represents a validation rule to check if a field's value is
 * one of a set of valid values
 */
class InRule implements Rule
{
    /**
     * Checks if the given field value is one of the set of valid values.
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
        if (empty($params)) {
            throw new InvalidArgumentException(
                'specify a list of valid values'
            );
        }
        return in_array($data[$field], $params);
    }
    /**
     * Gets the error message for the InRule validation.
     * 
     * @param array $data
     * @param string $field
     * @param array $params
     * 
     * @return string
     */
    public function getMessage(array $data, string $field, array $params)
    {
        $validValues = implode(', ', $params);

        return "The value of {$field} must be one of the following: {$validValues}";
    }
}
