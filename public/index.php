<?php
session_start();

use App\Core\Core;
use App\Core\Config;
use App\Core\Routing\Router;

require_once __DIR__ . '/../vendor/autoload.php';

(new Core(new Config(), new Router()))->start();
