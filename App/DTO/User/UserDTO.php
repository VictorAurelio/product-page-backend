<?php

/**
 * This file is part of my Product Page project.
 *
 * @category  DTO
 * @package   App\DTO\User
 * @author    Victor Aurélio Rodrigues Ribeiro <victoraurelio_198@hotmail.com>
 * @copyright (c) 2023 Victor Aurelio
 * @link      https://github.com/VictorAurelio/product-page
 */

namespace App\DTO\User;

use App\DTO\DTOInterface;

/**
 * Summary of UserDTO
 */
class UserDTO implements DTOInterface
{
    /**
     * Summary of email
     * @var
     */
    private $_email;
    /**
     * Summary of password
     * @var
     */
    private $_password;
    /**
     * Summary of name
     * @var
     */
    private $_name;
    private $_id;
    public function getId()
    {
        return $this->_id;
    }
    public function setId($id)
    {
        $this->_id = $id;
    }
    /**
     * Summary of getEmail
     * @return mixed
     */
    public function getEmail()
    {
        return $this->_email;
    }
    /**
     * Summary of setEmail
     * @param mixed $email
     * @return void
     */
    public function setEmail($email)
    {
        $this->_email = $email;
    }
    /**
     * Summary of getPassword
     * @return mixed
     */
    public function getPassword()
    {
        return $this->_password;
    }
    /**
     * Summary of setPassword
     * @param mixed $password
     * @return void
     */
    public function setPassword($password)
    {
        $this->_password = $password;
    }
    /**
     * Summary of getName
     * @return mixed
     */
    public function getName()
    {
        return $this->_name;
    }
    /**
     * Summary of setName
     * @param mixed $name
     * @return void
     */
    public function setName($name)
    {
        $this->_name = $name;
    }
    /**
     * Summary of toArray
     * @return array
     */
    public function toArray(): array
    {
        return [
            'email' => $this->_email,
            'password' => $this->_password,
            'name' => $this->_name,
        ];
    }
}
