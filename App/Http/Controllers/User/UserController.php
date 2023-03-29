<?php

/**
 * This file is part of my Product Page project.
 *
 * @category  Controller
 * @package   App\Http\Controllers\User
 * @author    Victor Aurélio Rodrigues Ribeiro <victoraurelio_198@hotmail.com>
 * @copyright (c) 2023 Victor Aurelio
 * @link      https://github.com/VictorAurelio/product-page
 */

namespace App\Http\Controllers\User;

use App\Core\Database\DatabaseService\DatabaseService;
use App\Core\Validation\Exception\ValidationException;
use App\Core\Database\QueryBuilder\MysqlQueryBuilder;
use App\Core\Database\Connection\ConnectionInterface;
use App\Core\Validation\Rule\NoWhitespaceRule;
use App\Http\Controllers\User\LogoutUserController;
use App\Core\Validation\Rule\Data\DataSanitizer;
use App\Core\Validation\Rule\RequiredRule;
use App\Core\Validation\Rule\UniqueRule;
use App\Core\Database\DAO\User\UserDAO;
use App\Core\Validation\Rule\MatchRule;
use App\Core\Validation\Rule\EmailRule;
use App\Core\Database\DatabaseFactory;
use App\Core\Validation\Rule\MinRule;
use App\Core\Validation\Validator;
use App\Core\Database\DAO\DAO;
use App\Models\User\UserModel;
use App\Auth\Authentication;
use App\DTO\User\UserDTO;
use App\Core\Controller;

/**
 * This class manages user-related operations
 */
class UserController extends Controller
{
    /**
     * An instance of ConnectionInterface used for connecting to the database.
     * 
     * @var ConnectionInterface
     */
    protected ConnectionInterface $connection;
    /**
     * An instance of Authentication used for user authentication with JWT.
     * 
     * @var Authentication
     */
    protected Authentication $authentication;
    /**
     * An instance of DataSanitizer used for sanitizing data.
     * 
     * @var DataSanitizer
     */
    protected DataSanitizer $sanitizer;
    /**
     * An instance of Validator.
     * 
     * @var Validator
     */
    protected Validator $validator;
    /**
     * An instance of UserModel.
     * 
     * @var UserModel
     */
    protected UserModel $userModel;
    /**
     * An instance of UserDAO
     * 
     * @var UserDAO
     */
    protected UserDAO $userDAO;
    /**
     * An instance of DAO.
     * 
     * @var DAO
     */
    protected DAO $dao;
    /**
     * Initializes the UserController by setting up a database connection,
     * user model, user DAO, validator, authentication, and sanitizer.
     */
    public function __construct()
    {
        $this->connection = DatabaseFactory::createConnection();
        $this->userModel = new UserModel($this->connection);
        $this->dao = new DAO(
            new DatabaseService($this->connection),
            new MysqlQueryBuilder($this->connection),
            $this->userModel->getTableSchema(),
            $this->userModel->getTableSchemaId()
        );
        $this->userDAO = new UserDAO($this->dao);
        $this->validator = new Validator();
        $this->validator
            ->addRule(
                'unique',
                new UniqueRule(
                    $this->connection,
                    'users',
                    'email'
                )
            )
            ->addRule('email', new EmailRule())
            ->addRule('required', new RequiredRule())
            ->addRule('no_whitespace', new NoWhitespaceRule())
            ->addRule('match', new MatchRule())
            ->addRule('min', new MinRule());

        $this->authentication = new Authentication();
        $this->sanitizer = new DataSanitizer();
    }
    /**
     * Summary of index
     * 
     * @return void
     */
    public function index()
    {
        $this->redirect('/home');
    }
    /**
     * Validates access method and user input and instantiates the
     * register controller. Returns a JSON response with a success
     * or error message and status code according to the result received
     * from the RegisterUserController.
     * 
     * @return void
     */
    public function signUp()
    {
        if ($this->getMethod() !== 'POST') {
            $this->json(['message' => 'Invalid method for signing up'], 405);
        }

        // Read the request data
        $payload = $this->getRequestData();
        $data = $this->sanitizer->clean($payload);

        $registerUserController = new RegisterUserController($this);

        try {
            $result = $registerUserController->register($data);
            $this->json([
                'message' => $result['message'],
                'jwt' => $result['jwt'],
                'userId' => $result['userId']
            ], $result['status']);
        } catch (ValidationException $e) {
            $this->json($e->getErrors(), 400);
        }
    }
    /**
     * Create a UserDTO object from sanitized data
     *
     * @param array $data
     *
     * @return UserDTO
     */
    protected function createUserDTO(array $data): UserDTO
    {
        $userDTO = new UserDTO();
        $userDTO->setName($data['name']);
        $userDTO->setEmail($data['email']);
        $userDTO->setPassword($data['password']);

        return $userDTO;
    }
    /**
     * Similar to the signUp, this function validates request method and user input
     * before instantiating the login controller. Returns a JSON response with a
     * success or error message and status code, according to the results received
     * from the LoginUserController.
     * 
     * @return void
     */
    public function signIn()
    {
        if ($this->getMethod() !== 'POST') {
            $this->json(['message' => 'Invalid method for signing in'], 405);
        }

        // Read the request data
        $payload = $this->getRequestData();
        $data = $this->sanitizer->clean($payload);

        $loginUserController = new LoginUserController($this);

        try {
            $result = $loginUserController->login($data);
            $this->json([
                'message' => $result['message'],
                'jwt' => $result['jwt'],
                'userId' => $result['userId'],
            ], $result['status']);
        } catch (ValidationException $e) {
            $this->json($e->getErrors(), 400);
        }
    }
    /**
     * This method first check if the request method is correct and then validates
     * the actual JWT token, so we can make sure the token being passed
     * is valid and belongs to the user logged in. Just additional validation,
     * probably won't needed in this project.
     * 
     * @return void
     */
    public function logoutValidate()
    {
        if ($this->getMethod() !== 'POST') {
            $this->json(
                [
                    'message' => 'Invalid method for logging out'
                ],
                [
                    'status' => 405
                ]
            );
        }
        $authorizationHeader = $this->authentication->getAuthorizationHeader();
        $jwt = $this->authentication->getBearerToken($authorizationHeader);

        $logoutUserController = new LogoutUserController($this);
        $isLogoutSuccessful = $logoutUserController->logout($jwt);

        if ($isLogoutSuccessful) {
            $this->json(['message' => 'Logout successful'], ['status' => 201]);
        } else {
            $this->json(
                [
                    'message' => 'Error logging out. Please try again.'
                ],
                [
                    'status' => 400
                ]
            );
        }
    }
    /**
     * This function is responsible for refreshing the JWT token of the logged user
     * according to some actions our user will do, to stay logged in.
     * 
     * @return void
     */
    public function refreshToken()
    {
        // Verify the request method
        if ($this->getMethod() !== 'POST') {
            $this->json(
                [
                    'message' => 'Invalid method for refreshing token'
                ],
                405
            );
        }

        // Verify if the current JWT is valid
        $authorizationHeader = $this->authentication->getAuthorizationHeader();
        $currentJwt = $this->authentication->getBearerToken($authorizationHeader);
        $userIdFromJwt = $this->userModel->getUserIdFromJwt($currentJwt);

        if ($userIdFromJwt === false) {
            $this->json(['message' => 'Invalid token'], 401);
        }

        // Create a new JWT and return it
        $newJwt = $this->userModel->createJwt($userIdFromJwt);

        $this->json(['jwt' => $newJwt], 200);
    }

    /**
     * 
     * @return bool
     */
    public function verifyAuthentication()
    {
        // Verify if the current JWT is valid
        $authorizationHeader = $this->authentication->getAuthorizationHeader();
        $currentJwt = $this->authentication->getBearerToken($authorizationHeader);
        $userIdFromJwt = $this->userModel->getUserIdFromJwt($currentJwt);

        return $userIdFromJwt !== false;
    }
}