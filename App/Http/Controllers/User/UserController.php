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

class UserController extends Controller
{
    protected ConnectionInterface $connection;
    protected Authentication $authentication;
    protected DataSanitizer $sanitizer;
    protected Validator $validator;
    protected UserModel $userModel;
    protected UserDAO $userDAO;
    protected DAO $dao;
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
            ->addRule('unique', new UniqueRule($this->connection, 'users', 'email'))
            ->addRule('email', new EmailRule())
            ->addRule('required', new RequiredRule())
            ->addRule('match', new MatchRule())
            ->addRule('min', new MinRule());

        $this->authentication = new Authentication();
        $this->sanitizer = new DataSanitizer();
    }
    public function index()
    {
        $users = $this->userDAO->read(['name', 'email']);
        var_dump($users);
        // $this->redirect('/home');
    }
    // public function view($id)
    // {
    //     $array = ['error' => '', 'logged' => false];

    //     $method = $this->getMethod();
    //     $payload = $this->getRequestData();
    //     var_dump($payload);
    //     if (!empty($payload['jwt']) && $this->userModel->validateJwt($payload['jwt'])) {
    //         $array['logged'] = true;
    //         echo 'logado';
    //         $array['self'] = false;
    //         if ($id == $this->userModel->getUserIdFromJwt($payload['jwt'])) {
    //             $array['self'] = true;
    //         }
    //     } else {
    //         $array['error'] = 'acesso negado';
    //     }

    //     // $this->json($array);
    // }
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
    public function createUserDTO(array $data): UserDTO
    {
        $userDTO = new UserDTO();
        $userDTO->setName($data['name']);
        $userDTO->setEmail($data['email']);
        $userDTO->setPassword($data['password']);

        return $userDTO;
    }
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
    public function logoutValidate()
    {
        if ($this->getMethod() !== 'POST') {
            $this->json(['message' => 'Invalid method for logging out'], ['status' => 405]);
        }
        $authorizationHeader = $this->authentication->getAuthorizationHeader();
        $jwt = $this->authentication->getBearerToken($authorizationHeader);

        $logoutUserController = new LogoutUserController($this);
        $isLogoutSuccessful = $logoutUserController->logout($jwt);

        if ($isLogoutSuccessful) {
            $this->json(['message' => 'Logout successful'], ['status' => 201]);
        } else {
            $this->json(['message' => 'Error logging out. Please try again.'], ['status' => 400]);
        }
    }
    public function refreshToken()
    {
        // Verify the request method
        if ($this->getMethod() !== 'POST') {
            $this->json(['message' => 'Invalid method for refreshing token'], 405);
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

    public function verifyAuthentication()
    {
        // Verify if the current JWT is valid
        $authorizationHeader = $this->authentication->getAuthorizationHeader();
        $currentJwt = $this->authentication->getBearerToken($authorizationHeader);
        $userIdFromJwt = $this->userModel->getUserIdFromJwt($currentJwt);

        return $userIdFromJwt !== false;
    }
}
