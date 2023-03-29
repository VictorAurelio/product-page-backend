<?php

/**
 * This file is part of my Product Page project.
 *
 * @category  Controller
 * @package   App\Http\Controllers\Error
 * @author    Victor Aurélio Rodrigues Ribeiro <victoraurelio_198@hotmail.com>
 * @copyright (c) 2023 Victor Aurelio
 * @link      https://github.com/VictorAurelio/product-page
 */

namespace App\Http\Controllers\Error;

use App\Core\Controller;

/**
 * This controller is responsible for handling errors in the application
 * by sending the appropriate HTTP response code and message to the client.
 */
class ErrorHandlerController extends Controller
{
    /**
     * sets the HTTP response code to 404 (Not Found) and sends
     * a JSON message to the client saying that the page was not found.
     * 
     * @return void
     */
    public function pageNotFound()
    {
        http_response_code(404);
        $this->json(['message' => 'Page not found.'], 404);
    }
    /**
     * sets the HTTP response code to 400 (Bad Request) and sends a JSON message
     * to the client saying that the parameters were invalid.
     * 
     * @return void
     */
    public function invalidParameters()
    {
        http_response_code(400);
        $this->json(['message' => 'Invalid parameters.'], 400);
    }
    /**
     * sets the HTTP response code to 400 (Bad Request) and sends a JSON message to
     * the client saying that the request was invalid.
     * 
     * @return void
     */
    public function handleInvalidRequest() {
        http_response_code(400);
        $this->json(['message' => 'Invalid Request.'], 400);
    }
}
