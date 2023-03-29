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

/**
 * The Validator class is responsible for validating data based on a set of rules.
 * It has an array $rules to store all the validation rules with their
 * respective aliases. It provides a method addRule() that allows you to add new
 * validation rules to the $rules array.
 */
class Validator
{
    /**
     * Summary of rules
     * 
     * @var array
     */
    protected array $rules = [];
    /**
     * The addRule method allows you to add a new rule to the $rules array.
     * It receives a string $alias that will be used to identify
     * the validation rule, and a Rule object that implements the validate
     * and getMessage methods. The validate method returns an array with
     * the data keys that passed the validation, based on the $rules array.
     * 
     * @param string $alias
     * @param Rule $rule
     * 
     * @return static
     */
    public function addRule(string $alias, Rule $rule): static
    {
        $this->rules[$alias] = $rule;
        return $this;
    }
    /**
     * The validate method is the core of the class, it receives an array $data
     * with the data to be validated, an array $rules with the validation rules to
     * be applied to each field of the data, and an optional string $sessionName to
     * set a name for the session that will store the validation errors. The method
     * iterates over each field and its associated rules, checks if the rule exists
     * in the $rules array and if it does, it retrieves the corresponding validation
     * rule object and executes its validate method passing the data,
     * the field name, and the rule parameters. If the validation fails, the method
     * retrieves the corresponding error message from the rule object and adds it
     * to the $errors array. Finally, if there are any validation errors, it throws
     * a ValidationException with the $errors array and the $sessionName.
     * 
     * @param array $data
     * @param array $rules
     * @param string $sessionName
     * @return array
     */
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
