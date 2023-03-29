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
     * private _email
     * @var
     */
    private $_email;
    /**
     * private _password
     * @var
     */
    private $_password;
    /**
     * private _name
     * @var
     */
    private $_name;
    /**
     * private _id
     * @var
     */
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
     * retrieve the user email
     * 
     * @return mixed
     */
    public function getEmail()
    {
        return $this->_email;
    }
    /**
     * modify the user email
     * 
     * @param mixed $email
     * 
     * @return void
     */
    public function setEmail($email)
    {
        $this->_email = $email;
    }
    /**
     * retrieve the user password
     * 
     * @return mixed
     */
    public function getPassword()
    {
        return $this->_password;
    }
    /**
     * modify the user's password
     * 
     * @param mixed $password
     * 
     * @return void
     */
    public function setPassword($password)
    {
        $this->_password = $password;
    }
    /**
     * retrieve the user name
     * 
     * @return mixed
     */
    public function getName()
    {
        return $this->_name;
    }
    /**
     * modify the user's name
     * 
     * @param mixed $name
     * 
     * @return void
     */
    public function setName($name)
    {
        $this->_name = $name;
    }
    /**
     * returns an array with the UserDTO's properties.
     * 
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
