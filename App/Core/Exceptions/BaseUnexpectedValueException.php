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

class AppUnexpectedValueException extends Exception
{
    private string $errorMessage;

    public function __construct(string $errorMessage)
    {
        $this->errorMessage = $errorMessage;
        parent::__construct($errorMessage);
    }

    public function getErrorJson(): string
    {
        return json_encode(['error' => $this->errorMessage]);
    }
}