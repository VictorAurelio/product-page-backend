<?php

/**
 * This file is part of my Product Page project.
 *
 * @category  Route
 * @package   App\Core\Routing
 * @author    Victor Aurélio Rodrigues Ribeiro <victoraurelio_198@hotmail.com>
 * @copyright (c) 2023 Victor Aurelio
 * @link      https://github.com/VictorAurelio/product-page
 */

return [

    '/' => '/product/showAllProducts',

    '/user/sign-in' => '/user/signIn',

    '/user/sign-up' => '/user/signUp',

    '/user/profile/{id}' => '/user/view/:id',

    '/user/logout' => '/user/logoutValidate',

    '/user/refresh/' => '/user/refreshToken',

    '/add-product' => '/product/handleAddProduct',

    '/edit-product/{id}' => '/product/handleUpdateProduct/:id',

    '/product/massDelete' => '/product/handleMassDelete',

];
