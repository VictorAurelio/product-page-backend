<?php

/**
 * This file is part of my Product Page project.
 *
 * @category  Model
 * @package   App\Models\Product
 * @author    Victor Aurélio Rodrigues Ribeiro <victoraurelio_198@hotmail.com>
 * @copyright (c) 2023 Victor Aurelio
 * @link      https://github.com/VictorAurelio/product-page
 */

namespace App\Models\Product;

use App\DTO\DTOInterface;
use App\DTO\Product\FurnitureDTO;

/**
 * Furniture model class which extends Product model and contains the Furniture's
 * specific attribute
 */
class Furniture extends Product
{
    /**
     * Predefined Furniture category, but could be made dynamic if we wanted to expand
     * the system and add brands for example.
     * 
     * @return int
     */
    public function getCategoryId(): int
    {
        return 3;
    }
    /**
     * Return the Furniture's dimensions via getter connected to it's DTO.
     * 
     * @param DTOInterface $productDTO
     * 
     * @return array
     */
    public function specificAttributes(DTOInterface $productDTO): array
    {
        /** @var FurnitureDTO $productDTO */
        return [
            'dimensions' => $productDTO->getDimensions(),
        ];
    }
}