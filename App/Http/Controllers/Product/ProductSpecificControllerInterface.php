<?php

/**
 * This file is part of my Product Page project.
 *
 * @category  Interface
 * @package   App\Http\Controllers\Product
 * @author    Victor Aurélio Rodrigues Ribeiro <victoraurelio_198@hotmail.com>
 * @copyright (c) 2023 Victor Aurelio
 * @link      https://github.com/VictorAurelio/product-page
 */

namespace App\Http\Controllers\Product;

use App\DTO\DTOInterface;
/**
 * This interface defines the methods that a product-specific controller
 * must implement. These methods include inserting and updating products,
 * creating a DTO from data and an option value, and getting the DAO object.
 */
interface ProductSpecificControllerInterface
{
    public function insertProduct(array $data): array;
    public function updateProduct(int $productId, array $data): array;
    public function createDTO(array $data, float $optionValue): DTOInterface;
    public function getDAO();
}
