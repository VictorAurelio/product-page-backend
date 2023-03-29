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
use App\Core\Database\DAO\Product\FurnitureDAO;
use App\Models\ProductOption\ProductOption;
use App\DTO\Product\ProductOptionDTO;
use App\DTO\Product\FurnitureDTO;
use App\Models\Product\Furniture;
use App\DTO\DTOInterface;

/**
 * The FurnitureController class implements the ProductSpecificControllerInterface
 * and contains methods related to handling requests for Furniture products.
 */
class FurnitureController implements ProductSpecificControllerInterface
{
    /**
     * an instance of ProductController
     * 
     * @var ProductController
     */
    protected ProductController $productController;
    /**
     * an instance of Furniture
     * 
     * @var Furniture
     */
    protected Furniture $productModel;
    /**
     * an instance of FurnitureDAO
     * 
     * @var FurnitureDAO
     */
    protected FurnitureDAO $furnitureDAO;
    /**
     * The __construct method accepts an instance of ProductController
     * and a Furniture object. It initializes the ProductController and Furniture
     * objects and creates a new FurnitureDAO object with the Furniture object
     * passed as argument.
     * 
     * @param ProductController $productController
     * @param Furniture $furnitureModel
     */
    public function __construct(ProductController $productController, Furniture $furnitureModel)
    {
        $this->productController = $productController;
        $this->productModel = new Furniture($this->productController->getConnection());
        $this->furnitureDAO = new FurnitureDAO($furnitureModel);
    }
    /**
     * This function is part of the FurnitureController class and it is responsible
     * for inserting a new Furniture into the database. It receives an array with the
     * Furniture's data and first applies a data cleaning process and then a validation
     * process to ensure that the data is in the correct format.
     * 
     * Then, it gets the corresponding option ID for the Furniture type, creates a
     * FurnitureDTO object and calls the create method from FurnitureDAO to insert
     * the new Furniture into the database.
     * 
     * After that, it creates a new ProductOptionDTO object with the product's ID,
     * the option ID and the Furniture's dimensions, and calls the createOption method from
     * the ProductOption class to insert this information into the database.
     * 
     * Finally, it returns an array with a success or error message and a
     * corresponding HTTP status code.
     * 
     * @param array $data
     * 
     * @return array
     */
    public function insertProduct(array $data): array
    {
        $data = $this->productController->getSanitizer()->clean($data);
        $this->productController->getValidator()->validate($data, [
            'name' => ['required'],
            'sku' => ['required', 'unique', 'no_whitespace'],
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
    /**
     * The updateProduct method in the FurnitureController receives an array of data
     * to update a Furniture in the database. The method starts by sanitizing the data
     * and then validating it using the Validator object. It also checks for the
     * current SKU of the product that is being updated and uses it to ensure the
     * uniqueness of the SKU and if the SKU hasn't been modified,
     * it won't call an error message.
     * 
     * The method then gets the ID of the corresponding option for the
     * product type, creates a FurnitureDTO object using the sanitized data and option
     * value, and calls the update method of the FurnitureDAO object to update the
     * Furniture in the database.
     * 
     * After that, the method retrieves the ID of the ProductOption object for the
     * product type and ID and updates its value with the new size using the
     * updateOption method of the ProductOption object.
     * 
     * Finally, the method returns a result array with a success
     * or error message and status code.
     * 
     * @param int $productId
     * @param array $data
     * 
     * @return array
     */
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
            'sku' => ['required', "exist:products,sku,$currentSku", 'no_whitespace'],
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

        if (!$productOption && !$updatedFurniture) {
            return ['message' => 'Error updating furniture', 'status' => 400];
        }

        return ['message' => 'Furniture updated successfully', 'status' => 201];
    }
    /**
     * The createDTO method is a helper method that creates and returns
     * a new FurnitureDTO object with the given data and option value.
     * 
     * @param array $data
     * @param float $dimensionsFloat
     * 
     * @return DTOInterface
     */
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
    /**
     * The getDAO method returns the FurnitureDAO object used by the FurnitureController.
     * 
     * @return FurnitureDAO
     */
    public function getDAO(): FurnitureDAO
    {
        return $this->furnitureDAO;
    }
}
