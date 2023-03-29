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
 * This is a class that implements the Rule interface and is used to validate
 * whether two fields in a dataset match.
 */
class MatchRule implements Rule
{
    /**
     * This method is used to validate whether two fields in a dataset match.
     * It takes in the data, the field to validate, and the field to compare with,
     * and throws an exception if the compare field is empty. It returns a boolean
     * value depending on whether the two fields match.
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
                'specify a field to compare'
            );
        }

        $compareField = $params[0];

        return $data[$field] === $data[$compareField];
    }
    /**
     * This method returns an error message if the validation fails.
     * It takes in the data, the field to validate, and the field to compare with,
     * and returns a string that says the two fields do not match.
     * 
     * @param array $data
     * @param string $field
     * @param array $params
     * 
     * @return string
     */
    public function getMessage(array $data, string $field, array $params)
    {
        $compareField = $params[0];

        return json_encode(
            [
                "message" => "{$field} and {$compareField} do not match"
            ]
        );
    }
}
