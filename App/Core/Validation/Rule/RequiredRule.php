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

use App\Core\Validation\Rule\Rule;
class RequiredRule implements Rule
{
    public function validate(array $data, string $field, array $params)
    {
        return !empty($data[$field]);
    }

    public function getMessage(array $data, string $field, array $params)
    {
        return "{$field} is required";
    }
}
