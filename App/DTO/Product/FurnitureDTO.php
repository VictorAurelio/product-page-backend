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
 * Summary of FurnitureDTO
 */
class FurnitureDTO extends ProductDTO
{
    private string $_dimensions;
      
    public function getDimensions(): string
    {
        return $this->_dimensions;
    }
    public function setDimensions(string $dimensions): self
    {
        $this->_dimensions = $dimensions;
        return $this;
    }
    /**
     * Helper function intended to separate the dimensions values.
     * Usage example: $dimensionsArray = $furnitureDTO->getDimensionsAsArray();
     * list($height, $width, $length) = $dimensionsArray;
     * 
     * @return array
     */
    public function getDimensionsAsArray(): array
    {
        return explode('x', $this->_dimensions);
    }
    public function toArray(): array
    {
        $parentArray = parent::toArray();
        return array_merge($parentArray, [
            'dimensions' => $this->_dimensions,
        ]);
    }
}