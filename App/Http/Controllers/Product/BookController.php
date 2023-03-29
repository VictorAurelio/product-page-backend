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
use App\Core\Database\DAO\Product\BookDAO;
use App\DTO\Product\ProductOptionDTO;
use App\DTO\Product\BookDTO;
use App\Models\Product\Book;
use App\DTO\DTOInterface;

/**
 * The BookController class implements the ProductSpecificControllerInterface
 * and contains methods related to handling requests for book products. 
 */
class BookController implements ProductSpecificControllerInterface
{
    /**
     * an instance of ProductController
     * 
     * @var ProductController
     */
    protected ProductController $productController;
    /**
     * an instance of Book.
     * 
     * @var Book
     */
    protected Book $productModel;
    /**
     * an instance of BookDAO.
     * 
     * @var BookDAO
     */
    protected BookDAO $bookDAO;
    /**
     * The __construct method accepts an instance of ProductController
     * and a Book object. It initializes the ProductController and Book
     * objects and creates a new BookDAO object with the Book object
     * passed as argument.
     * 
     * @param ProductController $productController
     * @param Book $bookModel
     */
    public function __construct(ProductController $productController, Book $bookModel)
    {
        $this->productController = $productController;
        $this->productModel = new Book($this->productController->getConnection());
        $this->bookDAO = new BookDAO($bookModel);
    }
    /**
     * This function is part of the BookController class and it is responsible
     * for inserting a new book into the database. It receives an array with the
     * book's data and first applies a data cleaning process and then a validation
     * process to ensure that the data is in the correct format.
     * 
     * Then, it gets the corresponding option ID for the book type, creates a
     * BookDTO object and calls the create method from BookDAO to insert
     * the new book into the database.
     * 
     * After that, it creates a new ProductOptionDTO object with the product's ID,
     * the option ID and the book's weight, and calls the createOption method from
     * the ProductOption class to insert this information into the database.
     * 
     * Finally, it returns an array with a success or error message and a
     * corresponding HTTP status code.
     * 
     * @param array $data
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
            'weight' => ['required', 'not_null']
        ]);
        // Get the ID of the corresponding option for the product type
        $optionId = $this->productController->getOptionIdByType('Book');
        $bookDTO = $this->createDTO($data, $data['weight']);

        // Create the book and get the last inserted book ID
        $book = $this->bookDAO->create($bookDTO);

        $productOptionDTO = new ProductOptionDTO();
        $productOptionDTO->setProductId($book);
        $productOptionDTO->setOptionId($optionId);
        $productOptionDTO->setOptionValue($data['weight']);

        $productOption = new ProductOption($this->productController->getConnection());
        $productOption->createOption($productOptionDTO);

        $result = match (true) {
            !$book => ['message' => 'Error creating book', 'status' => 500],
            default => ['message' => 'Book created successfully', 'status' => 201],
        };
        return $result;
    }
    /**
     * The updateProduct method in the BookController receives an array of data
     * to update a book in the database. The method starts by sanitizing the data
     * and then validating it using the Validator object. It also checks for the
     * current SKU of the product that is being updated and uses it to ensure the
     * uniqueness of the SKU and if the SKU hasn't been modified,
     * it won't call an error message.
     * 
     * The method then gets the ID of the corresponding option for the
     * product type, creates a BookDTO object using the sanitized data and option
     * value, and calls the update method of the BookDAO object to update the
     * book in the database.
     * 
     * After that, the method retrieves the ID of the ProductOption object for the
     * product type and ID and updates its value with the new weight using the
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
            'weight' => ['required', 'numeric', 'not_null']
        ]);

        // Get the ID of the corresponding option for the product type
        $optionId = $this->productController->getOptionIdByType('Book');

        $bookDTO = $this->createDTO($data, $data['weight']);

        $updatedBook = $this->bookDAO->update($bookDTO, $productId);

        $productOption = new ProductOption($this->productController->getConnection());
        $productOptionId = $productOption->findByOptionId($optionId, $productId)->getId();

        $productOptionDTO = new ProductOptionDTO();
        $productOptionDTO->setId($productOptionId);
        $productOptionDTO->setProductId($productId);
        $productOptionDTO->setOptionId($optionId);
        $productOptionDTO->setOptionValue($data['weight']);

        $productOption->updateOption($productOptionDTO);

        if (!$productOption && !$updatedBook) {
            return ['message' => 'Error updating book', 'status' => 400];
        }
        return ['message' => 'Book updated successfully', 'status' => 201];
    }
    /**
     * The createDTO() method is a helper method that creates and returns
     * a new BookDTO object with the given data and option value.
     * 
     * @param array $data
     * @param mixed $optionValue
     * 
     * @return DTOInterface
     */
    public function createDTO(array $data, $optionValue): DTOInterface
    {
        $bookDTO = new BookDTO();
        $bookDTO->setId($data['id'] ?? null);
        $bookDTO->setName($data['name']);
        $bookDTO->setSku($data['sku']);
        $bookDTO->setPrice($data['price']);
        $bookDTO->setCategoryId($data['category_id']);
        $bookDTO->setWeight($optionValue);

        return $bookDTO;
    }
    /**
     * The getDAO() method returns the BookDAO object used by the BookController.
     * 
     * @return BookDAO
     */
    public function getDAO(): BookDAO
    {
        return $this->bookDAO;
    }
}
