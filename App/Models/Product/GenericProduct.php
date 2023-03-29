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

/**
 * Generic class intended to work as a helper for using Product's model properties
 * without having to call for any specific product instead.
 */
class GenericProduct extends Product
{
    /**
     * This declaration of specificAttributes won't be used in this model.
     * 
     * @param DTOInterface $productDTO
     * 
     * @return array
     */
    public function specificAttributes(DTOInterface $productDTO): array
    {
        return [];
    }
}