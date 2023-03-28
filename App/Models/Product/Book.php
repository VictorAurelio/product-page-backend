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

class Book extends Product
{
    public function getCategoryId(): int
    {
        return 1;
    }
    public function specificAttributes(DTOInterface $productDTO): array
    {
        /** @var BookDTO $productDTO */
        return [
            'weight' => $productDTO->getWeight(),
        ];
    }
}