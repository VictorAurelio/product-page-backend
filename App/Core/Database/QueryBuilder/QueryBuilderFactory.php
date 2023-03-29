<?php

/**
 * This file is part of my Product Page project.
 *
 * @category  Factory
 * @package   App\Core\Database\QueryBuilder
 * @author    Victor Aurélio Rodrigues Ribeiro <victoraurelio_198@hotmail.com>
 * @copyright (c) 2023 Victor Aurelio
 * @link      https://github.com/VictorAurelio/product-page
 */

namespace App\Core\Database\QueryBuilder;

use App\Core\Database\Connection\ConnectionInterface;
use App\Core\Database\Connection\MysqlConnection;

/**
 * This class, QueryBuilderFactory, provides a factory method that returns
 * an instance of QueryBuilderInterface.
 */
class QueryBuilderFactory
{
    /**
     * The method takes a ConnectionInterface parameter and creates an instance of
     * MysqlQueryBuilder if the connection type is MysqlConnection, otherwise it
     * throws an exception for unsupported connection types.
     * 
     * @param ConnectionInterface $connection
     * 
     * @throws \InvalidArgumentException
     * 
     * @return QueryBuilderInterface
     */
    public function create(ConnectionInterface $connection): QueryBuilderInterface
    {
        if ($connection instanceof MysqlConnection) {
            return new MysqlQueryBuilder($connection);
        }

        throw new \InvalidArgumentException('Unsupported connection type');
    }
}
