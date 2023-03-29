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
 * The BookDTO class extends the ProductDTO class and represents
 * a data transfer object for a book product.
 */
class BookDTO extends ProductDTO
{
    /**
     * private property _weight
     * 
     * @var float
     */
    private float $_weight;    
    /**
     * retrieve the weight of the product
     * 
     * @return float
     */
    public function getWeight(): float
    {
        return $this->_weight;
    }
    /**
     * modify the weight of the product
     * 
     * @param float $weight
     * 
     * @return void
     */
    public function setWeight(float $weight): void
    {
        $this->_weight = $weight;
    }
    /**
     * method returns an array representation of the book DTO object,
     * including its weight property.
     * 
     * @return array
     */
    public function toArray(): array
    {
        $parentArray = parent::toArray();
        return array_merge($parentArray, [
            'weight' => $this->_weight,
        ]);
    }
}