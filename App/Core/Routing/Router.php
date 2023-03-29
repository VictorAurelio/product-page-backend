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

namespace App\Core\Routing;

use App\Core\Core;

/**
 * A class for managing the routes of my application.
 */
class Router extends Core
{
    protected const PARAM_PATTERN = '(\{[a-z0-9]{1,}\})'; //'#{([^}]+)}/#' -- // #{id}/comments/# -> /posts/123/comments/
    protected const OPT_PARAM_PATTERN = '([^/]*)(?:/?)'; // /posts/{id}/{slug?} -> /posts/123 and /posts/123/hello-world
    protected const REQ_PARAM_PATTERN = '(\{|\})'; // /posts/{id}/comments/ -> /posts/123/comments/
    protected const MAT_PARAM_PATTERN = '([a-z0-9-]{1,})';
    /**
     * variable used to store the URL being routed
     * 
     * @var
     */
    protected $url;
    /**
     * variable used to store the application's routes
     * 
     * @var
     */
    protected $routes;
    /**
     * constructor method for the Router class that initializes the routes variable
     */
    public function __construct()
    {
        $this->routes = [];
    }
    /**
     * method for loading application routes from a file.
     * 
     * @param mixed $file
     * 
     * @return void
     */
    public function loadRoutes($file)
    {
        $this->routes = include($file);
    }
    /**
     * method for matching a given URL against the application's defined routes
     * and returning the matching URL with any associated parameters replaced.
     * 
     * @param mixed $url
     * 
     * @return mixed
     */
    public function checkRoutes($url)
    {
        foreach ($this->routes as $path => $newUrl) {
            // Identify the arguments and replace them for regex
            $pattern = preg_replace(self::PARAM_PATTERN, self::MAT_PARAM_PATTERN, $path);
            // match the url with the route
            if (preg_match('#^(' . $pattern . ')*$#i', $url, $matches) === 1) {
                array_shift($matches);
                array_shift($matches);
                // get the arguments to associate
                $items = [];
                if (preg_match_all(self::PARAM_PATTERN, $path, $m)) {
                    $items = preg_replace(self::REQ_PARAM_PATTERN, '', $m[0]);
                }
                // make the association
                $args = [];
                foreach ($matches as $key => $match) {
                    $args[$items[$key]] = $match;
                }
                // set the new url
                foreach ($args as $argKey => $argValue) {
                    $newUrl = str_replace(':' . $argKey, $argValue, $newUrl);
                }
                $url = $newUrl;
                break;
            }
        }
        return $url;
    }
}