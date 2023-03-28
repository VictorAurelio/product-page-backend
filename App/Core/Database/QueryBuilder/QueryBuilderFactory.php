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

class QueryBuilderFactory
{
    public function create(ConnectionInterface $connection): QueryBuilderInterface
    {
        if ($connection instanceof MysqlConnection) {
            return new MysqlQueryBuilder($connection);
        }
        
        throw new \InvalidArgumentException('Unsupported connection type');
    }
}