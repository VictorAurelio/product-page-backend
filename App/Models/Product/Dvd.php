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
use App\DTO\Product\DvdDTO;

/**
 * Dvd model class which extends Product model and contains the DVD's
 * specific attribute
 */
class Dvd extends Product
{
    /**
     * Predefined DVD category, but could be made dynamic if we wanted to expand
     * the system and add brands for example.
     * 
     * @return int
     */
    public function getCategoryId(): int
    {
        echo 'test';
        return 2;
    }
    /**
     * Return the DVD's size via getter connected to it's DTO.
     * 
     * @param DTOInterface $productDTO
     * 
     * @return array
     */
    public function specificAttributes(DTOInterface $productDTO): array
    {
        /** @var DvdDTO $productDTO */
        return [
            'size' => $productDTO->getSize(),
        ];
    }
}