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
 * This class implements the Rule interface and represents a validation rule for
 * checking if the specified field in an array of data has a valid format
 * for dimensions (HxWxL).
 */
class DimensionsFormatRule implements Rule
{
    /**
     * receives the data array, the field to validate, and an array
     * of optional parameters. It checks if the field has a valid format
     * and returns true or false depending on the result.
     * 
     * @param array $data
     * @param string $field
     * @param array $params
     * 
     * @return bool|int
     */
    public function validate(array $data, string $field, array $params)
    {
        if (empty($data[$field])) {
            return true;
        }

        $regex = '/^(\d+(\.\d+)?)(x(\d+(\.\d+)?)){2}$/';
        if (!preg_match($regex, $data[$field])) {
            return false;
        }

        // Verify if none of the values is equal to 0
        $dimensions = explode('x', $data[$field]);
        foreach ($dimensions as $dimension) {
            if ((float)$dimension == 0) {
                return false;
            }
        }

        return true;
    }
    /**
     * receives the data array, the field name, and an array of optional parameters
     * It returns a string with a message indicating that the specified field
     * should have a valid format for dimensions (HxWxL).
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
                "message" => "{$field} should have a valid format (HxWxL)
                                and none of the dimensions should be equal to 0"
            ]
        );
    }
}
