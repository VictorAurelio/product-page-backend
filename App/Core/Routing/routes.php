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

/*
    This is a configuration file that maps URLs to corresponding controller actions
    in my web application. Each URL is associated with a specific controller method
    that will be executed when that URL is accessed. The configuration includes URLs
    for various user authentication and product management operations...
*/

return [

    '/' => '/product/showAllProducts',

    '/user/sign-in' => '/user/signIn',

    '/user/sign-up' => '/user/signUp',

    '/user/logout' => '/user/logoutValidate',

    '/user/refresh/' => '/user/refreshToken',

    '/add-product' => '/product/handleAddProduct',

    '/edit-product/{id}' => '/product/handleUpdateProduct/:id',

    '/product/massDelete' => '/product/handleMassDelete',

];
