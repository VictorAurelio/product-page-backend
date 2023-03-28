<?php

/**
 * This file is part of my Product Page project.
 *
 * @category  Validator
 * @package   App\Core\Validation
 * @author    Victor Aurélio Rodrigues Ribeiro <victoraurelio_198@hotmail.com>
 * @copyright (c) 2023 Victor Aurelio
 * @link      https://github.com/VictorAurelio/product-page
 */

namespace App\Core\Validation;


use App\Core\Validation\Exception\ValidationException;
use App\Core\Validation\Rule\Rule;

class Validator
{
    protected array $rules = [];

    public function addRule(string $alias, Rule $rule): static
    {
        $this->rules[$alias] = $rule;
        return $this;
    }

    public function validate(array $data, array $rules, string $sessionName = 'errors'): array
    {
        $errors = [];

        foreach ($rules as $field => $rulesForField) {
            foreach ($rulesForField as $rule) {
                $name = $rule;
                $params = [];

                if (str_contains($rule, ':')) {
                    [$name, $params] = explode(':', $rule);
                    $params = explode(',', $params);
                }
                
                $processor = $this->rules[$name];

                if (!$processor->validate($data, $field, $params)) {
                    if (!isset($errors[$field])) {
                        $errors[$field] = [];
                    }

                    array_push($errors[$field], $processor->getMessage($data, $field, $params));
                }
            }
        }

        if (count($errors)) {
            $exception = new ValidationException();
            $exception->setErrors($errors);
            $exception->setSessionName($sessionName);
            throw $exception;
        } else {
            $msg = json_encode(['message' => 'success']);
        }
        return array_intersect_key($data, $rules);
    }
}
