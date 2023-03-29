<?php

/**
 * This file is part of my Product Page project.
 *
 * @category  Model
 * @package   App\Models\User
 * @author    Victor Aurélio Rodrigues Ribeiro <victoraurelio_198@hotmail.com>
 * @copyright (c) 2023 Victor Aurelio
 * @link      https://github.com/VictorAurelio/product-page
 */

namespace App\Models\User;

use App\Core\Database\Connection\ConnectionInterface;
use App\Core\Database\DAO\User\UserDAO;
use InvalidArgumentException;
use App\DTO\DTOInterface;
use App\DTO\User\UserDTO;
use App\Core\Model;
use App\Auth\Jwt;

/**
 * Model for handling user data and authentication
 */
class UserModel extends Model
{
    protected const TABLESCHEMA = 'users';
    protected const TABLESCHEMAID = 'id';
    /**
     * The database connection object
     * 
     * @var ConnectionInterface
     */
    protected ConnectionInterface $connection;
    /**
     * The data access object for users
     * 
     * @var UserDAO
     */
    protected UserDAO $userDAO;
    /**
     * The JSON Web Token object
     * 
     * @var Jwt
     */
    protected JWT $jwt;
    /**
     * The user ID
     * 
     * @var
     */
    private $userId;
    /**
     * Constructs the UserModel object and initializes it with the given
     * database connection object
     * 
     * @param ConnectionInterface $connection
     */
    public function __construct(ConnectionInterface $connection)
    {
        parent::__construct(self::TABLESCHEMA, self::TABLESCHEMAID, $connection);
        $this->userDAO = new UserDAO($this->dao);
        $this->jwt = new JWT();
    }
    /**
     * Stores the given user data in the database and returns the ID of the
     * newly created user.
     * 
     * @param DTOInterface $userDTO - The user data
     * 
     * @throws InvalidArgumentException - If not an instance of UserDTO
     * 
     * @return int|null
     */
    public function store(DTOInterface $userDTO)
    {
        if (!$userDTO instanceof UserDTO) {
            throw new InvalidArgumentException('Expected UserDTO instance.');
        }
        return $this->userDAO->create($userDTO);
    }
    /**
     * Checks the given user credentials and returns the user ID if the
     * credentials are valid, false otherwise.
     * 
     * @param mixed $email - The user's email
     * @param mixed $password - The user's password
     * 
     * @return bool|mixed
     */
    public function checkCredentials($email, $password)
    {
        $userDTO = new UserDTO();
        $userDTO->setEmail($email);
        $foundUserDTO = $this->userDAO->findByEmail($userDTO);

        if (
            $foundUserDTO !== null &&
            password_verify(
                $password,
                $foundUserDTO->getPassword()
            )
        ) {
            $this->userId = $foundUserDTO->getId();
            return $this->userId;
        }
        return false;
    }
    /**
     * Returns the table schema name for the user model.
     * 
     * @return string
     */
    public function getTableSchema()
    {
        return self::TABLESCHEMA;
    }
    /**
     * Returns the primary-key of this table
     * 
     * @return string
     */
    public function getTableSchemaId()
    {
        return self::TABLESCHEMAID;
    }
    /**
     * Returns the user ID from the given JWT token, or false
     * if the token is invalid.
     * 
     * @param mixed $token
     * 
     * @return mixed
     */
    public function getUserIdFromJwt($token)
    {
        $info = $this->jwt->validate($token);
        if (isset($info->userId)) {
            return $info->userId;
        } else {
            return false;
        }
    }
    /**
     * Validates the given JWT token and returns true if the token is valid,
     * false otherwise.
     * 
     * @param mixed $token
     * 
     * @return bool
     */
    public function validateJwt($token)
    {
        $info = $this->jwt->validate($token);
        // var_dump($info);
        if (isset($info->userId) && isset($info->exp) && time() < $info->exp) {
            $this->userId = $info->userId;
            return true;
        } else {
            return false;
        }
    }
    /**
     * Create a JWT according to the information we decide to pass in the array
     * and set it's expiration time for one week.
     * 
     * @param mixed $userId
     * 
     * @return string
     */
    public function createJwt($userId)
    {
        $expTime = time() + JWT_EXPIRATION_TIME;
        $token = $this->jwt->create(['userId' => $userId, 'exp' => $expTime]);
        return $token;
    }
}
