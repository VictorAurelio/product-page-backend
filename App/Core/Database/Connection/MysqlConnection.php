<?php

/**
 * This file is part of my Product Page project.
 *
 * @category  Mysql
 * @package   App\Core\Database\Connection
 * @author    Victor Aurélio Rodrigues Ribeiro <victoraurelio_198@hotmail.com>
 * @copyright (c) 2023 Victor Aurelio
 * @link      https://github.com/VictorAurelio/product-page
 */

namespace App\Core\Database\Connection;

use App\Core\Database\QueryBuilder\MysqlQueryBuilder;
use InvalidArgumentException;
use PDO;

/**
 * Mysql class responsible for Mysql connections with the database
 */
class MysqlConnection extends Connection
{
    private PDO $_pdo;
    private string $_database;
    /**
     * Make the connection to the database according to the data
     * received from the array.
     *
     * @param array $config Connection configuration
     * 
     * @throws InvalidArgumentException
     */
    public function __construct(array $config)
    {
        [
            'host' => $host,
            'database' => $database,
            'username' => $username,
            'password' => $password,
        ] = $config;

        if (empty($host) || empty($database) || empty($username)) {
            throw new InvalidArgumentException('Connection incorrectly configured');
        }
        $this->_database = $database;
        $this->_pdo = new PDO(
            "mysql:host={$host};dbname={$database}",
            $username,
            $password
        );
    }

    /**
     * Get the underlying Pdo instance for this connection
     * 
     * @return PDO The PDO instance for this connection.
     */
    public function pdo(): PDO
    {
        return $this->_pdo;
    }

    /**
     * Start a new query on this connection
     * 
     * @return MysqlQueryBuilder The query builder for this connection.
     */
    public function query(): MysqlQueryBuilder
    {
        return new MysqlQueryBuilder($this);
    }
}
