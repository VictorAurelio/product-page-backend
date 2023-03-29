<?php

/**
 * This file is part of my Product Page project.
 *
 * @category  Controller
 * @package   App\Core
 * @author    Victor Aurélio Rodrigues Ribeiro <victoraurelio_198@hotmail.com>
 * @copyright (c) 2023 Victor Aurelio
 * @link      https://github.com/VictorAurelio/product-page
 */

namespace App\Core;

use App\Core\Exceptions\AppInvalidRequestException;

/**
 * Main controller class which contains useful methods for those who inherit it.
 */
class Controller
{
    /**
     * retrieves the HTTP method used in the current request.
     * 
     * @return mixed
     */
    public function getMethod()
    {
        return $_SERVER['REQUEST_METHOD'];
    }
    /**
     * retrieves the request data from $_GET, $_POST, or the request body
     * (for PUT and DELETE requests) and returns it as an array.
     * 
     * @throws AppInvalidRequestException
     * 
     * @return array
     */
    public function getRequestData()
    {
        $method = $this->getMethod();
        $data = match ($method) {
            'GET' => $_GET,
            'POST' => $this->getJsonData() ?? $_POST,
            'PUT', 'DELETE' => $this->getJsonData(),
            default => null,
        };
        if (!is_array($data)) {
            throw new AppInvalidRequestException('Invalid request data');
        }
        return $data;
    }
    /**
     * retrieves JSON data from the request body and decodes it into an array.
     * 
     * @return mixed
     */
    private function getJsonData()
    {
        $data = file_get_contents('php://input');
        if (!$data) {
            return null;
        }
        $json = json_decode($data, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return null;
        }
        return $json;
    }
    /**
     * sends a JSON response with the given data and HTTP status code.
     * 
     * @param mixed $data
     * @param mixed $status
     * 
     * @return void
     */
    public function json($data, $status = 200)
    {
        ob_clean(); // Clear the output buffer
        http_response_code($status);
        header("Content-Type: application/json");
        echo json_encode($data);
    }
    /**
     * retrieves the base URL of the application.
     * 
     * @return string
     */
    private function getBaseUrl()
    {
        $base = (isset(
                $_SERVER['HTTPS']
            ) && strtolower(
                $_SERVER['HTTPS']
            ) == 'on'
        ) ? 'https://' : 'http://';

        $base .= $_SERVER['SERVER_NAME'];
        if ($_SERVER['SERVER_PORT'] != '80') {
            $base .= ':' . $_SERVER['SERVER_PORT'];
        }
        $base .= '/public';

        return $base;
    }
    /**
     * redirects the user to the given URL.
     * 
     * @param mixed $url
     * 
     * @return void
     */
    protected function redirect($url)
    {
        header("Location: " . $this->getBaseUrl() . $url);
    }
}
