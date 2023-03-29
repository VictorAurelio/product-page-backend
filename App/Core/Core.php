<?php

/**
 * This file is part of my Product Page project.
 *
 * @category  Core
 * @package   App\Core
 * @author    Victor Aurélio Rodrigues Ribeiro <victoraurelio_198@hotmail.com>
 * @copyright (c) 2023 Victor Aurelio
 * @link      https://github.com/VictorAurelio/product-page
 */

namespace App\Core;

use App\Http\Controllers\Error\ErrorHandlerController;
use App\Http\Controllers\HomeController;
use App\Core\Routing\Router;
use App\Core\Config;

/**
 * The Core class serves as the main entry point for the application
 * It loads the necessary dependencies and starts the application by
 * calling the router and controller classes.
 */
class Core
{
    /**
     * Summary of _errorController
     * @var ErrorHandlerController
     */
    private ErrorHandlerController $_errorController;
    /**
     * Summary of _homeController
     * @var HomeController
     */
    private HomeController $_homeController;
    /**
     * Summary of _config
     * @var Config
     */
    private Config $_config;
    /**
     * Summary of _router
     * @var Router
     */
    private Router $_router;
    /**
     * The constructor function initializes the Core class with the Config
     * and Router dependencies, sets up constants and the environment type.
     * 
     * @param Config $config The Config dependency
     * @param Router $router The Router dependency
     */
    public function __construct(Config $config, Router $router)
    {
        $this->_router = $router;
        $this->_config = $config;
        $this->_config->constants();
        $this->_config->environmentType();
    }

    /**
     * The start function is called to start the application. It configures CORS,
     * initializes the Error and Home controllers, and checks the URL for requested
     * routes, controllers and actions.
     * 
     * @return void
     */
    public function start()
    {
        $this->_config->configureCors();
        $this->_errorController = new ErrorHandlerController();
        $this->_homeController = new HomeController();
        $parameters = [];
        $url = '/';

        if (isset($_GET['url'])) {
            $url .= $_GET['url'];
        }

        $this->_router->loadRoutes('routes.php');
        $url = $this->_router->checkRoutes($url);

        if (empty($url) || $url == '/') {
            $currentController = $this->_homeController;
            $currentAction = DEFAULT_ACTION;
            $controller = new $currentController();
            call_user_func(array($controller, $currentAction), $parameters);
            return;
        }

        $url = explode('/', $url);
        array_shift($url);

        $currentController = match (true) {
            $url[0] === 'home' => "\App\Http\Controllers\HomeController",
            default => "\\App\\Http\\Controllers\\" . ucfirst($url[0]) . "\\" . (ucfirst($url[0]) . 'Controller'),
        };
        array_shift($url);

        $currentAction = (isset($url[0]) && !empty($url[0])) ? $url[0] : DEFAULT_ACTION;
        array_shift($url);

        if (count($url) > 0) {
            $parameters = $url;
        }

        if (!class_exists($currentController)) {
            $this->_errorController->pageNotFound();
            return;
        }

        $controller = new $currentController();
        if (!method_exists($controller, $currentAction)) {
            $this->_errorController->invalidParameters();
            return;
        }

        call_user_func_array(array($controller, $currentAction), $parameters);
    }
}
