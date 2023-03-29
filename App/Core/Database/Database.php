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
 * The Database class is responsible for managing the connection to the database.
 */
class Database
{
    /**
     * private static property $connection
     * 
     * @var ConnectionInterface|null
     */
    private static ?ConnectionInterface $connection = null;

    /**
     * A private constructor to prevent creating new instances of the class.
     */
    private function __construct()
    {
    }
    /**
     * A static method that returns the connection to the database.
     * If the connection hasn't been created yet, it calls the DatabaseFactory
     * to create a new connection.
     * 
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
