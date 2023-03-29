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
use App\DTO\Product\BookDTO;

/**
 * Book model class which extends Product model and contains the book's
 * specific attribute
 */
class Book extends Product
{
    /**
     * Predefined Book category, but could be made dynamic if we wanted to expand
     * the system and add brands for example.
     * 
     * @return int
     */
    public function getCategoryId(): int
    {
        return 1;
    }
    /**
     * Return the book's weight via getter connected to it's DTO.
     * 
     * @param DTOInterface $productDTO
     * 
     * @return array
     */
    public function specificAttributes(DTOInterface $productDTO): array
    {
        /** @var BookDTO $productDTO */
        return [
            'weight' => $productDTO->getWeight(),
        ];
    }
}