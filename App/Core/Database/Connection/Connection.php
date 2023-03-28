<?php

/**
 * This file is part of my Product Page project.
 *
 * @category  Database
 * @package   App\Core\Database\Connection
 * @author    Victor Aurélio Rodrigues Ribeiro <victoraurelio_198@hotmail.com>
 * @copyright Copyright (c) 2023 Victor Aurelio
 * @link      https://github.com/VictorAurelio/product-page
 */

namespace App\Core\Database\Connection;

use App\Core\Database\Connection\ConnectionInterface;
use App\Core\Database\QueryBuilder\QueryBuilder;
use PDO;

/**
 * Abstract class for database connections.
 */
abstract class Connection implements ConnectionInterface
{
    /**
     * Get the underlying Pdo instance for this connection
     * 
     * @return PDO The PDO instance for this connection.
     */
    abstract public function pdo(): PDO;
    /**
     * Start a new query on this connection
     * 
     * @return QueryBuilder The query builder for this connection.
     */
    abstract public function query(): QueryBuilder;
}
