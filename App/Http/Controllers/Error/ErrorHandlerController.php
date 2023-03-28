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

class ErrorHandlerController extends Controller
{
    public function pageNotFound()
    {
        http_response_code(404);
        $this->json(['message' => 'Page not found.'], 404);
    }

    public function invalidParameters()
    {
        http_response_code(400);
        $this->json(['message' => 'Invalid parameters.'], 400);
    }

    public function handleInvalidRequest() {
        http_response_code(400);
        $this->json(['message' => 'Invalid Request.'], 400);
    }
}
