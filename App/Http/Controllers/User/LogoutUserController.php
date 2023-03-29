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

use App\Http\Controllers\User\UserController;

/**
 * Controller for handling user logout and revoking authentication
 */
class LogoutUserController extends UserController
{
    /**
     * Constructs the LogoutUserController object and initializes
     * it with the UserController object.
     * 
     * @param UserController $userController
     */
    public function __construct(UserController $userController)
    {
        parent::__construct();
    }
    /**
     * Revokes the authentication for the user associated with the JWT token
     * and returns true if successful, false otherwise.
     * 
     * @param mixed $token - The JWT token of the user
     * 
     * @return bool
     */
    public function logout($token)
    {
        $userIdFromJwt = $this->userModel->getUserIdFromJwt($token);
    
        return $userIdFromJwt !== false;
    }
}