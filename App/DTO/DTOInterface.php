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
/**
 * This is an interface called DTOInterface that has a single method declaration
 * toArray(), which should return an array representing the DTO instance.
 * This interface is used to enforce consistency across DTO classes that
 * represent different types of data.
 */
interface DTOInterface
{
    public function toArray(): array;
}