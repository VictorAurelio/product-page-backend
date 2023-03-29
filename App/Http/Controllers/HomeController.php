<?php

/**
 * This file is part of my Product Page project.
 *
 * @category  Controller
 * @package   App\Http\Controllers
 * @author    Victor Aurélio Rodrigues Ribeiro <victoraurelio_198@hotmail.com>
 * @copyright (c) 2023 Victor Aurelio
 * @link      https://github.com/VictorAurelio/product-page
 */

namespace App\Http\Controllers;

use App\Core\Controller;

/**
 * HomeController
 */
class HomeController extends Controller
{
    /**
     * Summary of __construct
     */
    public function __construct()
    {
    }

    /**
     * Not needed since I'm using this home route already for showing all the
     * products but can be used for redirect if wanted.
     * 
     * @return void
     */
    public function index()
    {
        $this->redirect('/home');
    }
}