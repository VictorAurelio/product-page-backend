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

use App\Core\Database\DatabaseService\DatabaseService;
use App\Core\Validation\Exception\ValidationException;
use App\Core\Database\Connection\ConnectionInterface;
use App\Core\Database\QueryBuilder\MysqlQueryBuilder;
use App\Core\Validation\Rule\NotNullOrNegativeRule;
use App\Core\Validation\Rule\DimensionsFormatRule;
use App\Core\Validation\Rule\Data\DataSanitizer;
use App\Core\Database\DAO\Product\ProductDAO;
use App\Core\Validation\Rule\NoWhitespaceRule;
use App\Http\Controllers\User\UserController;
use App\Core\Validation\Rule\RequiredIfRule;
use App\Models\ProductOption\ProductOption;
use App\Core\Validation\Rule\RequiredRule;
use App\Core\Validation\Rule\NumericRule;
use App\Core\Validation\Rule\UniqueRule;
use App\Core\Validation\Rule\ExistRule;
use App\Core\Database\DatabaseFactory;
use App\Models\Product\GenericProduct;
use App\DTO\Product\ProductOptionDTO;
use App\Core\Validation\Rule\InRule;
use App\Core\Validation\Validator;
use App\Models\Product\Furniture;
use App\Models\Product\Product;
use App\Core\Database\DAO\DAO;
use InvalidArgumentException;
use App\Models\Product\Book;
use App\Models\Product\Dvd;
use App\Core\Controller;
use Exception;

/**
 * The ProductController is a class that extends the Controller class
 * and is responsible for managing products in the application
 */
class ProductController extends Controller
{
    /**
     * represents the database connection object
     * 
     * @var ConnectionInterface
     */
    protected ConnectionInterface $connection;
    /**
     * an instance of the UserController class
     * 
     * @var UserController
     */
    protected UserController $userController;
    /**
     * an instance of the GenericProduct class
     * 
     * @var GenericProduct
     */
    protected GenericProduct $productModel;
    /**
     * an instance of the DataSanitizer class
     * 
     * @var DataSanitizer
     */
    protected DataSanitizer $sanitizer;
    /**
     * an instance of the ProductDAO class
     * 
     * @var ProductDAO
     */
    protected ProductDAO $productDAO;
    /**
     * an instance of the Validator class
     * 
     * @var Validator
     */
    protected Validator $validator;
    /**
     * an instance of the DAO class
     * 
     * @var DAO
     */
    protected DAO $dao;
    /**
     * a constructor method that initializes the above objects and sets
     * up the required rules for the validator
     */
    public function __construct()
    {
        $this->connection = DatabaseFactory::createConnection();
        $this->sanitizer = new DataSanitizer();

        $this->dao = new DAO(
            new DatabaseService($this->getConnection()),
            new MysqlQueryBuilder($this->getConnection()),
            Product::TABLESCHEMA,
            Product::TABLESCHEMAID
        );
        $this->validator = new Validator();
        $this->validator
            ->addRule('unique', new UniqueRule(
                $this->getConnection(),
                Product::TABLESCHEMA,
                'sku'
            ))
            ->addRule('no_whitespace', new NoWhitespaceRule())
            ->addRule('required', new RequiredRule())
            ->addRule('exist', new ExistRule($this->getConnection()))
            ->addRule('required_if', new RequiredIfRule())
            ->addRule('dimensions', new DimensionsFormatRule())
            ->addRule('numeric', new NumericRule())
            ->addRule('not_null', new NotNullOrNegativeRule())
            ->addRule('in', new InRule());
        $this->productModel = new GenericProduct($this->getConnection());
        $this->productDAO = new ProductDAO($this->productModel);
        $this->userController = new UserController();
    }
    public function index()
    {
    }
    /**
     * is a function that handles mass deletion of products. It checks if the
     * request method is DELETE and if so, it calls the massDeleteProducts function
     * Otherwise, it returns a JSON response with an error message
     * and a 405 status code.
     * 
     * @return void
     */
    public function handleMassDelete()
    {
        $requestData = $this->getRequestData();
        $method = isset($requestData['_method']) &&
            strtoupper($requestData['_method']) ===
            'DELETE' ? 'DELETE' : $this->getMethod();

        if ($method !== 'DELETE') {
            $this->json(
                ['message' => 'Invalid method for mass delete'],
                405
            );
            return;
        }
        $this->massDeleteProducts();
    }
    /**
     * is a function that handles the addition of a product. If the request
     * method is GET, it returns a JSON response with a message saying that
     * the form should be displayed on the frontend. If the request method is
     * POST, it calls the insertProduct function to add the product. Otherwise,
     * it returns a JSON response with an error message and a 405 status code.
     * 
     * @return void
     */
    public function handleAddProduct()
    {
        $method = $this->getMethod();
        if ($method === 'GET') {
            // Frontend will handle the form rendering when accessed through GET.
            header('Content-Type: application/json');
            $this->json(
                ['message' => 'Form should be displayed on frontend'],
                200
            );
            return;
        }

        if ($method !== 'POST') {
            $this->json(
                ['message' => 'Invalid method for adding product'],
                405
            );
            return;
        }

        $this->insertProduct();
    }

    /**
     * is a function that handles updating a product. It first checks if the user
     * is authenticated, then checks the request method. If the request method is
     * PUT, it calls the updateProduct() function. If the request method is GET,
     * it calls the getProductById() function. Otherwise, it returns a JSON
     * response with an error message and a 405 status code.
     * 
     * @param mixed $productId
     * 
     * @return void
     */
    public function handleUpdateProduct($productId)
    {
        // Verify if the user is authenticated
        if (!$this->userController->verifyAuthentication()) {
            $this->json(['message' => 'Unauthorized'], 401);
            return;
        }

        $requestData = $this->getRequestData();
        $method = isset($requestData['_method']) &&
            strtoupper($requestData['_method']) ===
            'PUT' ? 'PUT' : $this->getMethod();

        if ($method === 'PUT') {
            $this->updateProduct($productId);
            return;
        }

        if ($method === 'GET') {
            $this->getProductById($productId);
            return;
        }

        $this->json(
            ['message' => 'Invalid method for updating product'],
            405
        );
    }

    /**
     * This function is responsible for inserting a new product into the database.
     * It first sanitizes the request data and then validates it to ensure that the
     * data is in the correct format. Then, it retrieves the product type and
     * category ID, and calls the insertProduct method of the corresponding
     * controller to insert the new product into the database.
     * 
     * After that, it retrieves the option ID for the product type, creates a
     * ProductOptionDTO object with the product's ID, the option ID, and the
     * product's specific option value, and calls the createOption method from
     * the ProductOption class to insert this information into the database.
     * 
     * Finally, it returns an array with a success or error message and a
     * corresponding HTTP status code.
     * 
     * @throws InvalidArgumentException
     * 
     * @return void
     */
    public function insertProduct()
    {
        if ($this->getMethod() !== 'POST') {
            $this->json(
                ['message' => 'Invalid method for inserting product'],
                405
            );
        }
        // Read the request data
        $payload = $this->getRequestData();
        $data = $this->sanitizer->clean($payload);

        try {
            // Validate the necessary data
            $this->validator->validate($data, [
                'product_type' => ['required', 'in:Book,Dvd,Furniture'],
                'weight' => ['required_if:product_type,Book'],
                'size' => ['required_if:product_type,Dvd'],
                'dimensions' => [
                    'required_if:product_type,Furniture', 'dimensions'
                ],
            ]);

            // Call the correct controller according to the product type
            $productController = $this
                                    ->getControllerInstance($data['product_type']);

            $data['category_id'] = match ($data['product_type']) {
                'Book' => 1,
                'Dvd' => 2,
                'Furniture' => 3,
                default => throw new InvalidArgumentException(
                    'Invalid product type'
                ),
            };
            $result = $productController->insertProduct($data);

            if (is_array($result) && isset($result['id'])) {
                $productId = $result['id'];

                // Get the ID of the corresponding option for the product type
                $optionId = $this->getOptionIdByType($data['product_type']);

                // Get the option value based on the product type
                $optionValueKey = match ($data['product_type']) {
                    'Book' => 'weight',
                    'Dvd' => 'size_in_mb',
                    'Furniture' => 'dimensions',
                    default => throw new InvalidArgumentException(
                        'Invalid product type'
                    ),
                };

                // Save the selected option for this product
                $productOptionDTO = new ProductOptionDTO();
                $productOptionDTO->setProductId($productId);
                $productOptionDTO->setOptionId($optionId);
                $productOptionDTO->setOptionValue($data[$optionValueKey]);

                $productOption = new ProductOption($this->getConnection());
                $productOption->createOption($productOptionDTO);
            }
            $this->json(
                ['message' => $result['message']],
                $result['status']
            );
        } catch (ValidationException $e) {
            $this->json(
                $e->getErrors(),
                400
            );
        } catch (InvalidArgumentException $e) {
            $this->json(
                ['message' => $e->getMessage()],
                400
            );
        }
    }
    /**
     * This function is responsible for updating a single product in the database
     * given a product ID and an array of updated data. It first checks that the
     * request method is POST, and then reads and sanitizes the request data.
     * 
     * It then validates the necessary data using a Validator object, and finally
     * calls the updateProduct() function of the appropriate product controller
     * (determined by the product type in the request data), passing in the
     * product ID and the sanitized data.
     * 
     * The function then returns a JSON response with a success or error message
     * and a corresponding status code.
     * 
     * @param mixed $productId
     * 
     * @return void
     */
    public function updateProduct($productId)
    {
        if ($this->getMethod() !== 'POST') {
            $this->json(
                ['message' => 'Invalid method for updating product'],
                405
            );
        }
        // Read the request data
        $payload = $this->getRequestData();
        $data = $this->sanitizer->clean($payload);

        try {
            // Validate the necessary data
            $this->validator->validate($data, [
                'product_type' => ['required', 'in:Book,Dvd,Furniture'],
                'price' => ['numeric', 'not_null'],
                'weight' => ['numeric'],
                'size' => ['numeric'],
                'dimensions' => ['dimensions'],
            ]);
            // Update the product
            $productController = $this
                ->getControllerInstance($data['product_type']);

            $result = $productController->updateProduct($productId, $data);

            $this->json(
                ['message' => $result['message']],
                $result['status']
            );
        } catch (ValidationException $e) {
            $this->json(
                $e->getErrors(),
                400
            );
        } catch (InvalidArgumentException $e) {
            $this->json(
                ['message' => $e->getMessage()],
                400
            );
        }
    }
    /**
     * This function is responsible for deleting multiple products from the
     * database. The function starts by checking if the request method is a
     * POST, and returns an error message with the corresponding HTTP status
     * code if it is not.
     * 
     * After verifying the request method, the function reads the request data
     * and sanitizes it using the clean method of the sanitizer object.
     * The function then attempts to delete the products by calling the
     * deleteProductsByIds method, passing the product IDs as an argument.
     * 
     * If the deletion is successful, the function returns a success message with
     * the number of products deleted and a corresponding HTTP status code. If an
     * exception is caught, the function returns an error message with
     * a corresponding HTTP status code.
     * 
     * @return void
     */
    public function massDeleteProducts()
    {
        if ($this->getMethod() !== 'POST') {
            $this->json(
                ['message' => 'Invalid method for mass delete'],
                405
            );
            return;
        }

        // Read the request data
        $payload = $this->getRequestData();
        $productIds = $this->sanitizer->clean($payload['product_ids']);

        try {
            $deletedCount = $this->deleteProductsByIds($productIds);
            if ($deletedCount > 1) {
                $this->json(
                [
                    'status' => 201,
                    'message' => "{$deletedCount} products deleted successfully"
                ],
                    201
                );
            } else {
                $this->json(
                    [
                        'status' => 201,
                        'message' => "{$deletedCount} product deleted successfully"
                    ],
                    201
                );
            }
            return;
        } catch (Exception $e) {
            $this->json(
                ['message' => $e->getMessage()],
                400
            );
            return;
        }
    }

    /**
     * The deleteProductsByIds method receives an array of product IDs and is
     * responsible for deleting all of them from the database
     * 
     * @param array $productIds
     * 
     * @return int
     */
    public function deleteProductsByIds(array $productIds): int
    {
        $deletedCount = 0;
        $result = $this->productDAO->deleteByIds($productIds);

        if ($result) {
            $deletedCount = count($productIds);
        }

        return $deletedCount;
    }
    /**
     * This method receives a string argument that represents a product
     * type and returns an instance of the respective controller that handles the
     * product type. It uses the match expression to create a new product model
     * based on the product type and then, based on the same product type, it
     * returns the respective controller instance, using the $class variable.
     * Finally, it returns an instance of the controller class, passing $this
     * and $productModel as parameters.
     * 
     * @param string $type
     * 
     * @throws InvalidArgumentException
     * 
     * @return ProductSpecificControllerInterface
     */
    private function getControllerInstance(
        string $type
    ): ProductSpecificControllerInterface {
        $productModel = match ($type) {
            'Book' => new Book($this->getConnection()),
            'Dvd' => new Dvd($this->getConnection()),
            'Furniture' => new Furniture($this->getConnection()),
            default => throw new InvalidArgumentException("Invalid product type"),
        };

        $class = match ($type) {
            'Book' => BookController::class,
            'Dvd' => DvdController::class,
            'Furniture' => FurnitureController::class,
            default => throw new InvalidArgumentException("Invalid product type"),
        };
        return new $class($this, $productModel);
    }
    /**
     * This method receives a string argument that represents a product type and
     * returns the ID of the corresponding option based on the product type.
     * It uses the match expression to return the correct option ID based
     * on the product type.
     * 
     * @param string $type
     * 
     * @return int
     */
    public function getOptionIdByType(string $type): int
    {
        return match ($type) {
            'Book' => 1, // Weight in (kg) ID for book
            'Dvd' => 2, // Size in (MB) ID for DVDs
            'Furniture' => 3, // Dimensions in (HxWxL) ID for furniture
            default => throw new InvalidArgumentException('Invalid product type.'),
        };
    }
    /**
     * This method receives a product ID as an argument and returns the product
     * details in JSON format. It first gets all the products by calling the
     * getAllProducts method and then iterates through the array to find the
     * product with the matching ID. If found, it returns the product details in
     * JSON format, with a 200 HTTP status code. If the product is not found, it
     * returns an error message with a 404 HTTP status code.
     * 
     * @param mixed $productId
     * 
     * @return void
     */
    public function getProductById($productId)
    {
        $allProducts = $this->getAllProducts();

        $product = null;
        foreach ($allProducts as $item) {
            if ($item['id'] == $productId) {
                $product = $item;
                break;
            }
        }

        if ($product) {
            $this->json($product, 200);
        } else {
            $this->json(['message' => 'Product not found'], 404);
        }
    }
    /**
     * This method is used to retrieve all products in the database. It does this
     * by creating instances of the BookController, DvdController,
     * and FurnitureController classes, and then calling their respective DAO
     * methods to get all books, DVDs, and furnitures in the database. It then
     * merges the results into one array and sorts them by descending ID.
     * The method then returns the sorted array of products.
     * 
     * @return array
     */
    public function getAllProducts(): array
    {
        $bookController = $this->getControllerInstance('Book');
        $dvdController = $this->getControllerInstance('Dvd');
        $furnitureController = $this->getControllerInstance('Furniture');

        $books = $bookController->getDAO()->getAllBooks();
        $dvds = $dvdController->getDAO()->getAllDvds();
        $furnitures = $furnitureController->getDAO()->getAllFurnitures();

        $allProducts = array_merge($books, $dvds, $furnitures);

        usort($allProducts, function ($a, $b) {
            return $b['id'] - $a['id'];
        });

        return $allProducts;
    }
    /**
     * This method is used to display all products to the user. It does this by
     * calling the getAllProducts method, and then returns the sorted array of
     * products as a JSON response with a status code of 200.
     * 
     * @return void
     */
    public function showAllProducts()
    {
        $allProducts = $this->getAllProducts();

        $this->json($allProducts, 200);
    }
    /**
     * This method returns the instance of the ConnectionInterface
     * used by the controller.
     * 
     * @return ConnectionInterface
     */
    public function getConnection(): ConnectionInterface
    {
        return $this->connection;
    }
    /**
     * This method returns an instance of the ProductDAO used by the controller.
     * 
     * @return ProductDAO
     */
    private function getProductDAO(): ProductDAO
    {
        return $this->productDAO;
    }
    /**
     * This method returns an instance of the Validator used by the controller.
     * 
     * @return Validator
     */
    public function getValidator(): Validator
    {
        return $this->validator;
    }
    /**
     * This method returns an instance of the DataSanitizer used by the controller.
     * 
     * @return DataSanitizer
     */
    public function getSanitizer(): DataSanitizer
    {
        return $this->sanitizer;
    }
}
