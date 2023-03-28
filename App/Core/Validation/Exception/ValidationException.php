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

class ValidationException extends InvalidArgumentException
{
    protected array $errors = [];
    protected string $sessionName = 'errors';

    public function setErrors(array $errors): static
    {
        $this->errors = $errors;
        return $this;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function setSessionName(string $sessionName): static
    {
        $this->sessionName = $sessionName;
        return $this;
    }

    public function getSessionName(): string
    {
        return $this->sessionName;
    }
}
