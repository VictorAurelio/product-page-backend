<?php

namespace App\Core\Validation\Rule;

class DimensionsFormatRule implements Rule
{
    public function validate(array $data, string $field, array $params)
    {
        if (empty($data[$field])) {
            return true;
        }

        $regex = '/^\d+(\.\d+)?x\d+(\.\d+)?x\d+(\.\d+)?$/';
        return preg_match($regex, $data[$field]);
    }

    public function getMessage(array $data, string $field, array $params)
    {
        return "{$field} should have a valid format (HxWxL)";
    }
}