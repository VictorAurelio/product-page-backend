<?php

/**
 * This file is part of my Product Page project.
 *
 * @category  Interface
 * @package   App\DTO
 * @author    Victor Aurélio Rodrigues Ribeiro <victoraurelio_198@hotmail.com>
 * @copyright (c) 2023 Victor Aurelio
 * @link      https://github.com/VictorAurelio/product-page
 */

namespace App\DTO;

interface DTOInterface
{
    public function toArray(): array;
}