<?php

/**
 * This file is part of my Product Page project.
 *
 * @category  Interface
 * @package   App\Core\Validation\Rule
 * @author    Victor Aurélio Rodrigues Ribeiro <victoraurelio_198@hotmail.com>
 * @copyright (c) 2023 Victor Aurelio
 * @link      https://github.com/VictorAurelio/product-page
 */

namespace App\Core\Validation\Rule;

interface Rule
{
    public function validate(array $data, string $field, array $params);
    public function getMessage(array $data, string $field, array $params);
}
