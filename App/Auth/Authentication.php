<?php

/**
 * This file is part of my Product Page project.
 *
 * @category  Authentication
 * @package   App\Auth
 * @author    Victor Aurélio Rodrigues Ribeiro <victoraurelio_198@hotmail.com>
 * @copyright (c) 2023 Victor Aurelio
 * @link      https://github.com/VictorAurelio/product-page
 */

namespace App\Auth;

/**
 * The Authentication class provides two methods for handling authentication.
 */
class Authentication
{
    /**
     * The function returns the authorization header from the HTTP request.
     * It checks if the header is available in three different ways,
     * returning the first one found. If none are found, it returns null.
     * 
     * @return string|null
     */
    public function getAuthorizationHeader()
    {
        if (isset($_SERVER['Authorization'])) {
            return trim($_SERVER["Authorization"]);
        }

        if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            return trim($_SERVER["HTTP_AUTHORIZATION"]);
        }

        if (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            $requestHeaders = array_combine(
                array_map('ucwords', array_keys($requestHeaders)),
                array_values($requestHeaders)
            );

            if (isset($requestHeaders['Authorization'])) {
                return trim($requestHeaders['Authorization']);
            }
        }
        return null;
    }
    /**
     * The function extracts the Bearer token from the authorization header passed
     * as a parameter. It uses a regular expression to match the token format
     * and returns it if found.
     * 
     * @param mixed $authorizationHeader
     * 
     * @return mixed
     */
    public function getBearerToken($authorizationHeader)
    {
        if (empty($authorizationHeader)) {
            return null;
        }

        if (!preg_match('/Bearer\s(\S+)/', $authorizationHeader, $matches)) {
            return null;
        }

        return $matches[1];
    }
}
