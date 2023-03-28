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
use InvalidArgumentException;

class MinRule implements Rule
{
    public function validate(array $data, string $field, array $params)
    {
        if (empty($data[$field])) {
            return true;
        }

        if (empty($params[0])) {
            throw new InvalidArgumentException('specify a min length');
        }

        $length = (int) $params[0];

        return strlen($data[$field]) >= $length;
    }

    public function getMessage(array $data, string $field, array $params)
    {
        $length = (int) $params[0];

        return "{$field} should be at least {$length} characters";
    }
}