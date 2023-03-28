<?php

/**
 * This file is part of my Product Page project.
 *
 * @category  Database
 * @package   App\Core\Database
 * @author    Victor Aurélio Rodrigues Ribeiro <victoraurelio_198@hotmail.com>
 * @copyright (c) 2023 Victor Aurelio
 * @link      https://github.com/VictorAurelio/product-page
 */

namespace App\Core;

use App\Core\Database\DatabaseFactory;
use App\Core\Database\Connection\ConnectionInterface;

/**
 * Summary of Database
 */
class Database
{
    private static ?ConnectionInterface $connection = null;

    private function __construct()
    {
    }

    /**
     * Summary of getConnection
     * @return ConnectionInterface
     */
    public static function getConnection(): ConnectionInterface
    {
        if (self::$connection === null) {
            self::$connection = DatabaseFactory::createConnection();
        }

        return self::$connection;
    }
}
