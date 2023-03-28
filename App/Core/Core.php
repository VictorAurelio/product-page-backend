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

class Core
{
    private ErrorHandlerController $_errorController;
    private HomeController $_homeController;
    private Config $_config;
    private Router $_router;
    public function __construct(Config $config, Router $router)
    {
        $this->_router = $router;
        $this->_config = $config;
        $this->_config->constants();
        $this->_config->environmentType();
    }

    public function start()
    {
        $this->_config->configureCors();
        $this->_errorController = new ErrorHandlerController();
        $this->_homeController = new HomeController();
        $parameters = [];
        $url = '/';
        // var_dump($url);

        if (isset($_GET['url'])) {
            $url .= $_GET['url'];
        }

        $this->_router->loadRoutes('routes.php');
        $url = $this->_router->checkRoutes($url);

        // var_dump($url);
        if (!empty($url) && $url != '/') {
            $url = explode('/', $url);
            array_shift($url);

            $currentController = match (true) {
                $url[0] === 'home' => "\App\Http\Controllers\HomeController",
                default => "\\App\\Http\\Controllers\\" . ucfirst($url[0]) . "\\" . (ucfirst($url[0]) . 'Controller'),
            };
            array_shift($url);

            $currentAction = (isset($url[0]) && !empty($url[0])) ? $url[0]  : DEFAULT_ACTION;
            array_shift($url);
            // var_dump($currentController);

            if (count($url) > 0) {
                $parameters = $url;
            }
        } else {
            $currentController = $this->_homeController;
            $currentAction = DEFAULT_ACTION;
            $controller = new $currentController();
            call_user_func(array($controller, $currentAction), $parameters);
            return;
        }
        // Verify if the controller actually exists and if not, redirect to 404 page
        if (class_exists($currentController)) {
            $controller = new $currentController();
            if (method_exists($controller, $currentAction)) {
                call_user_func_array(array($controller, $currentAction), $parameters);
            } else {
                $this->_errorController->invalidParameters();
            }
        } else {
            $this->_errorController->pageNotFound();
        }
    }
}
