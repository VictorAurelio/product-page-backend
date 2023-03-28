<?php

namespace App\Core\Validation\Rule;

use InvalidArgumentException;

class GreaterThanRule implements Rule
{
    public function validate(array $data, string $field, array $params)
    {
        if (!isset($data[$field])) {
            return true;
        }

        if (empty($params[0])) {
            throw new InvalidArgumentException('specify a number to be greater than');
        }

        $comparison = (float) $params[0];

        return (float) $data[$field] > $comparison;
    }

    public function getMessage(array $data, string $field, array $params)
    {
        $comparison = (float) $params[0];

        return json_encode(["message" => "{$field} should be greater than {$comparison}"]);
    }
}
