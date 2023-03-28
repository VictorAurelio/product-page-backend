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
 * Summary of UserModel
 */
class UserModel extends Model
{
    protected const TABLESCHEMA = 'users';
    protected const TABLESCHEMAID = 'id';
    protected ConnectionInterface $connection;
    protected UserDAO $userDAO;
    protected JWT $jwt;
    private $userId;
    public function __construct(ConnectionInterface $connection)
    {
        parent::__construct(self::TABLESCHEMA, self::TABLESCHEMAID, $connection);
        $this->userDAO = new UserDAO($this->dao);
        $this->jwt = new JWT();
    }

    public function store(DTOInterface $userDTO) {
        if (!$userDTO instanceof UserDTO) {
            throw new InvalidArgumentException('Expected UserDTO instance.');
        }
        return $this->userDAO->create($userDTO);
    }
    public function checkCredentials($email, $password)
    {
        $userDTO = new UserDTO();
        $userDTO->setEmail($email);
        $foundUserDTO = $this->userDAO->findByEmail($userDTO);
        
        if ($foundUserDTO !== null && password_verify($password, $foundUserDTO->getPassword())) {
            $this->userId = $foundUserDTO->getId();
            return $this->userId;
        }
        return false;
    }
    public function getTableSchema()
    {
        return self::TABLESCHEMA;
    }
    public function getTableSchemaId()
    {
        return self::TABLESCHEMAID;
    }
    public function getUserIdFromJwt($token)
    {
        $info = $this->jwt->validate($token);
        if (isset($info->userId)) {
            return $info->userId;
        } else {
            return false;
        }
    }
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
    public function createJwt($userId)
    {
        $expTime = time() + JWT_EXPIRATION_TIME;
        $token = $this->jwt->create(['userId' => $userId, 'exp' => $expTime]);
        return $token;
    }
}
