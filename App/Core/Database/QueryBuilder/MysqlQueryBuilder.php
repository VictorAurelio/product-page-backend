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

class MysqlQueryBuilder extends QueryBuilder
{
    protected MysqlConnection $connection;

    public function __construct(MysqlConnection $connection)
    {
        $this->connection = $connection;
    }

}
