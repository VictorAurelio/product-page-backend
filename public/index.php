<?php

/**
 * This file is part of my Product Page project.
 *
 * @category  Index
 * @package   public
 * @author    Victor Aurélio Rodrigues Ribeiro <victoraurelio_198@hotmail.com>
 * @copyright (c) 2023 Victor Aurelio
 * @link      https://github.com/VictorAurelio/product-page
 */

 /**
  * The index.php is the first file to be called in my application and it is
  * responsible for starting the session, requiring the composer's autoload
  * and starting the Core.php with our defined Config and Router classes.
  */
session_start();

use App\Core\Core;
use App\Core\Config;
use App\Core\Routing\Router;

require_once __DIR__ . '/../vendor/autoload.php';

(new Core(new Config(), new Router()))->start();