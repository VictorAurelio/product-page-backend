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

/**
 * The DvdController class implements the ProductSpecificControllerInterface
 * and contains methods related to handling requests for Dvd products.
 */
class DvdController implements ProductSpecificControllerInterface
{
    /**
     * an instance of ProductController
     * 
     * @var ProductController
     */
    protected ProductController $productController;
    /**
     * an instance of Dvd
     * 
     * @var Dvd
     */
    protected Dvd $productModel;
    /**
     * an instance of DvdDAO
     * 
     * @var DvdDAO
     */
    protected DvdDAO $dvdDAO;
    /**
     * The __construct method accepts an instance of ProductController
     * and a Dvd object. It initializes the ProductController and Dvd
     * objects and creates a new DvdDAO object with the Dvd object
     * passed as argument.
     * 
     * @param ProductController $productController
     * @param Dvd $dvdModel
     */
    public function __construct(ProductController $productController, Dvd $dvdModel)
    {
        $this->productController = $productController;
        $this->productModel = new Dvd($this->productController->getConnection());
        $this->dvdDAO = new DvdDAO($dvdModel);
    }
    /**
     * This function is part of the DvdController class and it is responsible
     * for inserting a new Dvd into the database. It receives an array with the
     * Dvd's data and first applies a data cleaning process and then a validation
     * process to ensure that the data is in the correct format.
     * 
     * Then, it gets the corresponding option ID for the Dvd type, creates a
     * DvdDTO object and calls the create method from DvdDAO to insert
     * the new Dvd into the database.
     * 
     * After that, it creates a new ProductOptionDTO object with the product's ID,
     * the option ID and the Dvd's size, and calls the createOption method from
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
    /**
     * The updateProduct method in the DvdController receives an array of data
     * to update a Dvd in the database. The method starts by sanitizing the data
     * and then validating it using the Validator object. It also checks for the
     * current SKU of the product that is being updated and uses it to ensure the
     * uniqueness of the SKU and if the SKU hasn't been modified,
     * it won't call an error message.
     * 
     * The method then gets the ID of the corresponding option for the
     * product type, creates a DvdDTO object using the sanitized data and option
     * value, and calls the update method of the DvdDAO object to update the
     * Dvd in the database.
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

        if (!$productOption && !$updatedDVD) {
            return ['message' => 'Error updating DVD', 'status' => 400];
        }

        return ['message' => 'DVD updated successfully', 'status' => 201];
    }
    /**
     * The createDTO method is a helper method that creates and returns
     * a new DvdDTO object with the given data and option value.
     * 
     * @param array $data
     * @param mixed $optionValue
     * 
     * @return DTOInterface
     */
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
    /**
     * The getDAO method returns the DvdDAO object used by the DvdController.
     * 
     * @return DvdDAO
     */
    public function getDAO(): DvdDAO
    {
        return $this->dvdDAO;
    }
}
