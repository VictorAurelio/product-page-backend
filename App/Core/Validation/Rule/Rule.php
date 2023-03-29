<?php

/**
 * This file is part of my Product Page project.
 *
 * @category  Interface
 * @package   App\Core\Validation\Rule
 * @author    Victor Aurélio Rodrigues Ribeiro <victoraurelio_198@hotmail.com>
 * @copyright (c) 2023 Victor Aurelio
 * @link      https://github.com/VictorAurelio/product-page
 */

namespace App\Core\Validation\Rule;

/**
 * This interface is implemented by all classes that define a validation
 * rule for a field in a form or data set
 */
interface Rule
{
    /**
     * receives an array of data, a string with the field name and an array
     * of parameters, and returns a boolean indicating if the
     * validation passed or not.
     * 
     * @param array $data
     * @param string $field
     * @param array $params
     * 
     * @return void
     */
    public function validate(array $data, string $field, array $params);
    /**
     * receives the same parameters as the validate method and returns a
     * string with the error message in case the validation fails.
     * 
     * @param array $data
     * @param string $field
     * @param array $params
     * 
     * @return void
     */
    public function getMessage(array $data, string $field, array $params);
}
