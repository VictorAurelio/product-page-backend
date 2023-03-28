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

class Dvd extends Product
{
    public function getCategoryId(): int
    {
        echo 'test';
        return 2;
    }
    public function specificAttributes(DTOInterface $productDTO): array
    {
        /** @var DvdDTO $productDTO */
        return [
            'size' => $productDTO->getSize(),
        ];
    }
}