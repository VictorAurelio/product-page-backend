<?php

/**
 * This file is part of my Product Page project.
 *
 * @category  Mysql
 * @package   App\Core\Database\QueryBuilder
 * @author    Victor Aurélio Rodrigues Ribeiro <victoraurelio_198@hotmail.com>
 * @copyright (c) 2023 Victor Aurelio
 * @link      https://github.com/VictorAurelio/product-page
 */

namespace App\Core\Database\QueryBuilder;

use App\Core\Database\Connection\MysqlConnection;

/**
 * MysqlQueryBuilder is a subclass of QueryBuilder that specifically handles
 * MySQL queries. It has a property called $connection of type MysqlConnection
 * and a constructor that takes an instance of MysqlConnection as an argument.
 */
class MysqlQueryBuilder extends QueryBuilder
{
    /**
     * protected property of MysqlQueryBuilder that holds
     * an instance of MysqlConnection.
     * 
     * @var MysqlConnection
     * 
     */
    protected MysqlConnection $connection;

    /**
     * The constructor of MysqlQueryBuilder takes an instance of
     * MysqlConnection as an argument and sets it to the $connection property
     * .
     * @param MysqlConnection $connection
     */
    public function __construct(MysqlConnection $connection)
    {
        $this->connection = $connection;
    }

}
