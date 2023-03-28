<?php

/**
 * This file is part of my Product Page project.
 *
 * @category  Interface
 * @package   App\Core\Database\Connection
 * @author    Victor Aurélio Rodrigues Ribeiro <victoraurelio_198@hotmail.com>
 * @copyright Copyright (c) 2023 Victor Aurelio
 * @link      https://github.com/VictorAurelio/product-page
 */

namespace App\Core\Database\Connection;

use App\Core\Database\QueryBuilder\QueryBuilder;
use PDO;

/**
 * ConnectionInterface defines the contract for database connection classes.
 * This ensures a consistent implementation across different connection types.
 */
interface ConnectionInterface
{
    /**
     * Get the underlying PDO instance for this connection.
     *
     * @return PDO The PDO instance associated with the connection.
     */
    public function pdo(): PDO;
    /**
     * Create a new query builder instance for this connection.
     *
     * @return QueryBuilder The query builder instance.
     */
    public function query(): QueryBuilder;
}
