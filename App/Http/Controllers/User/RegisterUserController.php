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

class RegisterUserController extends UserController
{
    public function __construct(UserController $userController)
    {
        parent::__construct();
    }

    public function register(array $data): array
    {
        $data = $this->sanitizer->clean($data);

        $this->validator->validate($data, [
            'name' => ['required'],
            'email' => ['required', 'unique', 'email'],
            'password' => ['required', 'min:8'],
            'password_confirmation' => ['required', 'match:password']
        ]);

        unset($data['password_confirmation']);

        $password_hash = password_hash($data['password'], PASSWORD_DEFAULT);
        $plain_password = $data['password'];
        $data['password'] = $password_hash;

        if (!$password_hash) {
            throw new \Exception('Error hashing password');
        }

        $userDTO = $this->createUserDTO($data);
        $user = $this->userDAO->create($userDTO);

        $userId = $this->userModel->checkCredentials($data['email'], $plain_password);
        
        $result = match (true) {
        !$user => ['message' => 'Error creating user', 'status' => 500],
        !$userId => ['message' => 'Invalid email or password', 'status' => 401],
        default => [
            'message' => 'User created successfully',
            'jwt' => $this->userModel->createJwt($userId),
            'userId' => $userId,
            'status' => 201
            ],
        };
        return $result;
    }
}
