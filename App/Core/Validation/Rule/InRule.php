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

class InRule implements Rule
{
    public function validate(array $data, string $field, array $params)
    {
        if (empty($params)) {
            throw new InvalidArgumentException('specify a list of valid values');
        }

        return in_array($data[$field], $params);
    }

    public function getMessage(array $data, string $field, array $params)
    {
        $validValues = implode(', ', $params);

        return "The value of {$field} must be one of the following: {$validValues}";
    }
}
