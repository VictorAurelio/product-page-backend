<?php

/**
 * This file is part of my Product Page project.
 *
 * @category  Exception
 * @package   App\Core\Exceptions
 * @author    Victor Aurélio Rodrigues Ribeiro <victoraurelio_198@hotmail.com>
 * @copyright (c) 2023 Victor Aurelio
 * @link      https://github.com/VictorAurelio/product-page
 */

namespace App\Core\Exceptions;

use Exception;

/**
 * This is the AppUnexpectedValueException class that extends the built-in
 * Exception class.
 */
class AppUnexpectedValueException extends Exception
{
    /**
     * holds the error message as a string.
     * 
     * @var string
     */
    private string $errorMessage;

    /**
     * takes in the error message as a parameter and assigns it
     * to the $errorMessage property.
     * 
     * @param string $errorMessage
     */
    public function __construct(string $errorMessage)
    {
        $this->errorMessage = $errorMessage;
        parent::__construct($errorMessage);
    }

    /**
     * returns the error message in JSON format as a string.
     * 
     * @return string
     */
    public function getErrorJson(): string
    {
        return json_encode(['error' => $this->errorMessage]);
    }
}