<?php

/**
 * This file is part of my Product Page project.
 *
 * @category  DTO
 * @package   App\DTO\Product
 * @author    Victor Aurélio Rodrigues Ribeiro <victoraurelio_198@hotmail.com>
 * @copyright (c) 2023 Victor Aurelio
 * @link      https://github.com/VictorAurelio/product-page
 */

namespace App\DTO\Product;

use App\DTO\Product\ProductDTO;

class BookDTO extends ProductDTO
{
    private float $_weight;    
    public function getWeight(): float
    {
        return $this->_weight;
    }
    public function setWeight(float $weight): void
    {
        $this->_weight = $weight;
    }
    public function toArray(): array
    {
        $parentArray = parent::toArray();
        return array_merge($parentArray, [
            'weight' => $this->_weight,
        ]);
    }
}