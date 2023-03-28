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

class DvdDTO extends ProductDTO
{
    private float $_size;    
    public function getSize(): float
    {
        return $this->_size;
    }
    public function setSize(float $size): void
    {
        $this->_size = $size;
    }
    public function toArray(): array
    {
        $parentArray = parent::toArray();
        return array_merge($parentArray, [
            'size' => $this->_size,
        ]);
    }
}