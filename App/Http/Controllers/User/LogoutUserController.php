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

class LogoutUserController extends UserController
{
    public function __construct(UserController $userController)
    {
        parent::__construct();
    }
    public function logout($token)
    {
        $userIdFromJwt = $this->userModel->getUserIdFromJwt($token);
    
        return $userIdFromJwt !== false;
    }
}