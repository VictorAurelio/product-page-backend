<?php

/**
 * This file is part of my Product Page project.
 *
 * @category  Exception
 * @package   App\Core\Validation\Exception
 * @author    Victor Aurélio Rodrigues Ribeiro <victoraurelio_198@hotmail.com>
 * @copyright (c) 2023 Victor Aurelio
 * @link      https://github.com/VictorAurelio/product-page
 */

namespace App\Core\Validation\Exception;

use InvalidArgumentException;

/**
 * An exception class that extends the InvalidArgumentException and adds an array
 * to store errors and a session name property. It provides methods to set and get
 * errors, as well as to set and get the session name.
 */
class ValidationException extends InvalidArgumentException
{
    /**
     * array property that stores validation errors.
     * 
     * @var array
     */
    protected array $errors = [];
    /**
     * string property that stores the name of the session variable used
     * to store validation errors.
     * 
     * @var string
     */
    protected string $sessionName = 'errors';

    /**
     * method that sets the validation errors stored in the errors property.
     * 
     * @param array $errors
     * 
     * @return static
     */
    public function setErrors(array $errors): static
    {
        $this->errors = $errors;
        return $this;
    }
    /**
     * method that returns the validation errors stored in the errors property.
     * 
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
    /**
     * method that sets the session variable name for validation errors.
     * 
     * @param string $sessionName
     * 
     * @return static
     */
    public function setSessionName(string $sessionName): static
    {
        $this->sessionName = $sessionName;
        return $this;
    }
    /**
     * method that returns the session variable name for validation errors.
     * 
     * @return string
     */
    public function getSessionName(): string
    {
        return $this->sessionName;
    }
}
