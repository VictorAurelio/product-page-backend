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
 * controller for handling user login and authentication
 */
class LoginUserController extends UserController
{
    /**
     * Constructs the LoginUserController object and initializes
     * it with the UserController object.
     * 
     * @param UserController $userController
     */
    public function __construct(UserController $userController)
    {
        parent::__construct();
    }
    /**
     * Authenticates user with given credentials and returns a JWT token,
     * authenticates the user and returns a JWT token along with the status code,
     * user ID and message.
     * 
     * This method also validates the received data with the pre-made rules.
     * *(App/Core/Validation/Rule)*
     * 
     * @param array $data - The user credentials
     * 
     * @return array
     */
    public function login(array $data): array
    {
        $data = $this->sanitizer->clean($data);

        $this->validator->validate($data, [
            'email' => ['required'],
            'password' => ['required']
        ]);

        $userId = $this->userModel
                        ->checkCredentials($data['email'], $data['password']);
        if (!$userId) {
            return ['message' => 'Invalid email or password', 'status' => 401];
        }
        $jwt = $this->userModel->createJwt($userId);
        return [
            'message' => 'User logged in successfully',
            'userId' => $userId,
            'jwt' => $jwt,
            'status' => 201
        ];
    }
}