<?php

/**
 * This file is part of my Product Page project.
 *
 * @category  Controller
 * @package   App\Http\Controllers\Product
 * @author    Victor Aurélio Rodrigues Ribeiro <victoraurelio_198@hotmail.com>
 * @copyright (c) 2023 Victor Aurelio
 * @link      https://github.com/VictorAurelio/product-page
 */

namespace App\Http\Controllers\Product;

use App\Core\Database\DAO\Product\ProductOptionDAO;
use App\Http\Controllers\Product\ProductSpecificControllerInterface;
use App\Http\Controllers\Product\ProductController;
use App\Core\Database\DAO\Product\FurnitureDAO;
use App\Models\ProductOption\ProductOption;
use App\DTO\Product\ProductOptionDTO;
use App\DTO\Product\FurnitureDTO;
use App\Models\Product\Furniture;
use App\DTO\DTOInterface;

class FurnitureController implements ProductSpecificControllerInterface
{
    protected ProductController $productController;
    protected Furniture $productModel;
    protected FurnitureDAO $furnitureDAO;
    public function __construct(ProductController $productController, Furniture $furnitureModel)
    {
        $this->productController = $productController;
        $this->productModel = new Furniture($this->productController->getConnection());
        $this->furnitureDAO = new FurnitureDAO($furnitureModel);
    }
    public function insertProduct(array $data): array
    {
        $data = $this->productController->getSanitizer()->clean($data);
        $this->productController->getValidator()->validate($data, [
            'name' => ['required'],
            'sku' => ['required', 'unique'],
            'price' => ['required', 'numeric', 'not_null'],
            'category_id' => ['required'],
            'dimensions' => ['required', 'dimensions', 'not_null']
        ]);
        // Get the ID of the corresponding option for the product type
        $optionId = $this->productController->getOptionIdByType('Furniture');

        // Convert dimensions string to float using crc32
        $dimensionsFloat = crc32($data['dimensions']);

        $furnitureDTO = $this->createDTO($data, $dimensionsFloat);

        // Create the furniture and get the last inserted furniture ID
        $furniture = $this->furnitureDAO->create($furnitureDTO);

        $productOptionDTO = new ProductOptionDTO();
        $productOptionDTO->setProductId($furniture);
        $productOptionDTO->setOptionId($optionId);
        $productOptionDTO->setOptionValue($data['dimensions']);

        $productOption = new ProductOption($this->productController->getConnection());
        $productOption->createOption($productOptionDTO);

        $result = match (true) {
            !$furniture => ['message' => 'Error creating furniture', 'status' => 500],
            default => ['message' => 'Furniture created successfully', 'status' => 201],
        };
        return $result;
    }
    public function updateProduct(int $productId, array $data): array
    {
        $data = $this->productController->getSanitizer()->clean($data);

        $allProducts = $this->productController->getAllProducts();
        $currentSku = null;
        foreach ($allProducts as $product) {
            if ($product['id'] == $productId) {
                $currentSku = $product['sku'];
                break;
            }
        }

        $this->productController->getValidator()->validate($data, [
            'sku' => ['required', "exist:products,sku,$currentSku"],
            'price' => ['required', 'numeric', 'not_null'],
            'category_id' => ['required'],
            'dimensions' => ['required', 'dimensions']
        ]);

        // Get the ID of the corresponding option for the product type
        $optionId = $this->productController->getOptionIdByType('Furniture');

        // Convert dimensions string to float using crc32
        $dimensionsFloat = crc32($data['dimensions']);

        $furnitureDTO = $this->createDTO($data, $dimensionsFloat);

        $updatedFurniture = $this->furnitureDAO->update($furnitureDTO, $productId);

        $productOption = new ProductOption($this->productController->getConnection());

        $productOptionId = $productOption->findByOptionId($optionId, $productId)->getId();

        $productOptionDTO = new ProductOptionDTO();
        $productOptionDTO->setId($productOptionId);
        $productOptionDTO->setProductId($productId);
        $productOptionDTO->setOptionId($optionId);
        $productOptionDTO->setOptionValue($data['dimensions']);

        $productOption->updateOption($productOptionDTO);

        if ($updatedFurniture || $productOption) {
            $result = ['message' => 'Furniture updated successfully', 'status' => 201];
        } else {
            $result = ['message' => 'Error updating furniture', 'status' => 500];
        }
        return $result;
    }
    public function createDTO(array $data, float $dimensionsFloat): DTOInterface
    {
        $furnitureDTO = new FurnitureDTO();
        $furnitureDTO->setId($data['id'] ?? null);
        $furnitureDTO->setName($data['name']);
        $furnitureDTO->setSku($data['sku']);
        $furnitureDTO->setPrice($data['price']);
        $furnitureDTO->setCategoryId($data['category_id']);
        $furnitureDTO->setDimensions($data['dimensions']);

        return $furnitureDTO;
    }
    public function getDAO(): FurnitureDAO
    {
        return $this->furnitureDAO;
    }
}
