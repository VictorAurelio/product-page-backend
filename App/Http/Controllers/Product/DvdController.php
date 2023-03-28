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

use App\Http\Controllers\Product\ProductSpecificControllerInterface;
use App\Http\Controllers\Product\ProductController;
use App\Models\ProductOption\ProductOption;
use App\Core\Database\DAO\Product\DvdDAO;
use App\DTO\Product\ProductOptionDTO;
use App\DTO\Product\DvdDTO;
use App\Models\Product\Dvd;
use App\DTO\DTOInterface;

class DvdController implements ProductSpecificControllerInterface
{
    protected ProductController $productController;
    protected Dvd $productModel;
    protected DvdDAO $dvdDAO;
    public function __construct(ProductController $productController, Dvd $dvdModel)
    {
        $this->productController = $productController;
        $this->productModel = new Dvd($this->productController->getConnection());
        $this->dvdDAO = new DvdDAO($dvdModel);
    }
    public function insertProduct(array $data): array
    {
        $data = $this->productController->getSanitizer()->clean($data);
        $this->productController->getValidator()->validate($data, [
            'name' => ['required'],
            'sku' => ['required', 'unique'],
            'price' => ['required', 'not_null'],
            'category_id' => ['required'],
            'size' => ['required', 'not_null']
        ]);
        // Get the ID of the corresponding option for the product type
        $optionId = $this->productController->getOptionIdByType('Dvd');
        $dvdDTO = $this->createDTO($data, $data['size']);
        // Create the dvd and get the last inserted dvd ID
        $dvd = $this->dvdDAO->create($dvdDTO);

        $productOptionDTO = new ProductOptionDTO();
        $productOptionDTO->setProductId($dvd);
        $productOptionDTO->setOptionId($optionId);
        $productOptionDTO->setOptionValue($data['size']);

        $productOption = new ProductOption($this->productController->getConnection());
        $productOption->createOption($productOptionDTO);

        $result = match (true) {
            !$dvd => ['message' => 'Error creating dvd', 'status' => 500],
            default => ['message' => 'Dvd created successfully', 'status' => 201],
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
            'price' => ['numeric', 'not_null'],
            'category_id' => ['required'],
            'size' => ['numeric', 'not_null']
        ]);

        // Get the ID of the corresponding option for the product type
        $optionId = $this->productController->getOptionIdByType('Dvd');

        $dvdDTO = $this->createDTO($data, $data['size']);

        $updatedDVD = $this->dvdDAO->update($dvdDTO, $productId);

        $productOption = new ProductOption($this->productController->getConnection());
        $productOptionId = $productOption->findByOptionId($optionId, $productId)->getId();

        $productOptionDTO = new ProductOptionDTO();
        $productOptionDTO->setId($productOptionId);
        $productOptionDTO->setProductId($productId);
        $productOptionDTO->setOptionId($optionId);
        $productOptionDTO->setOptionValue($data['size']);

        $productOption->updateOption($productOptionDTO);

        if ($updatedDVD || $productOption) {
            $result = ['message' => 'Dvd updated successfully', 'status' => 201];
        } else {
            $result = ['message' => 'Error updating dvd', 'status' => 500];
        }

        return $result;
    }
    public function createDTO(array $data, $optionValue): DTOInterface
    {
        $dvdDTO = new DvdDTO();
        $dvdDTO->setId($data['id'] ?? null);
        $dvdDTO->setName($data['name']);
        $dvdDTO->setSku($data['sku']);
        $dvdDTO->setPrice($data['price']);
        $dvdDTO->setCategoryId($data['category_id']);
        $dvdDTO->setSize($optionValue);

        return $dvdDTO;
    }
    public function getDAO(): DvdDAO
    {
        return $this->dvdDAO;
    }
}
