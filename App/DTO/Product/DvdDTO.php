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

/**
 * The DvdDTO class represents a data transfer object for a DVD product
 */
class DvdDTO extends ProductDTO
{
    /**
     * private property _size
     * 
     * @var float
     */
    private float $_size;    
    /**
     * retrieve the size of the product
     * 
     * @return float
     */
    public function getSize(): float
    {
        return $this->_size;
    }
    /**
     * modify the size of the product
     * 
     * @param float $size
     * 
     * @return void
     */
    public function setSize(float $size): void
    {
        $this->_size = $size;
    }
    /**
     * method is defined to return an array representation of the object,
     * including the size value.
     * 
     * @return array
     */
    public function toArray(): array
    {
        $parentArray = parent::toArray();
        return array_merge($parentArray, [
            'size' => $this->_size,
        ]);
    }
}