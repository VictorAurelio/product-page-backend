<?php

namespace App\Core;

use App\Core\Database\DatabaseService\DatabaseService;
use App\Core\Database\QueryBuilder\MysqlQueryBuilder;
use App\Core\Database\Connection\ConnectionInterface;
use App\Core\Exceptions\AppInvalidArgumentException;
use App\Core\Database\DatabaseFactory;
use App\Core\Database\DAO\DAO;

class Model
{
    protected ConnectionInterface $connection;
    protected string $tableSchemaId;
    protected string $tableSchema;
    protected DAO $dao;
    public function __construct(string $tableSchema, string $tableSchemaId, ConnectionInterface $connection)
    {
        if (empty($tableSchema || empty($tableSchemaId))) {
            throw new AppInvalidArgumentException('These arguments are required.');
        }
        $this->tableSchema = $tableSchema;
        $this->tableSchemaId = $tableSchemaId;
        $this->connection = $connection;
        $this->dao = new DAO(
            new DatabaseService($connection),
            new MysqlQueryBuilder($connection),
            $this->tableSchema,
            $this->tableSchemaId
        );
    }
    public function initialize(): object
    {
        $options = [];
        $handler = new DatabaseFactory($this->connection, $this->tableSchema, $this->tableSchemaId, $options);
        return $handler->initialize();
    }
}
