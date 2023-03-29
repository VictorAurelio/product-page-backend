<?php

/**
 * This file is part of my Product Page project.
 *
 * @category  Factory
 * @package   App\Core\Database
 * @author    Victor Aurélio Rodrigues Ribeiro <victoraurelio_198@hotmail.com>
 * @copyright (c) 2023 Victor Aurelio
 * @link      https://github.com/VictorAurelio/product-page
 */

namespace App\Core\Database;

use App\Core\Database\QueryBuilder\QueryBuilderFactory;
use App\Core\Database\Connection\MysqlConnection;
use App\Core\Database\Connection\ConnectionInterface;
use App\Core\Database\DatabaseService\DatabaseService;
use App\Core\Database\DAO\DAO;

/**
 * The DatabaseFactory class is responsible for creating a new database connection,
 * initializing a DAO instance and returning it.
 */
class DatabaseFactory
{
    protected ConnectionInterface $connection;
    protected string $tableSchemaId;
    protected string $tableSchema;
    protected array $options;

    /**
     * Constructor for the DatabaseFactory class that takes in a
     * ConnectionInterface object, a string representing the table schema, a string
     * representing the table schema ID, and an optional array of options.
     * The properties of the class are initialized with these parameters.
     * 
     * @param ConnectionInterface $connection
     * @param string $tableSchema
     * @param string $tableSchemaId
     * @param array|null $options
     */
    public function __construct(ConnectionInterface $connection, string $tableSchema, string $tableSchemaId, ?array $options = [])
    {
        $this->tableSchemaId = $tableSchemaId;
        $this->tableSchema = $tableSchema;
        $this->connection = $connection;
        $this->options = $options;
    }
    /**
     * This function creates a new database connection using the configuration data
     * stored in the constants DB_HOST, DB_NAME, DB_USER, and DB_PASS. It returns
     * an instance of the MysqlConnection class that implements the
     * ConnectionInterface.
     * 
     * @return ConnectionInterface
     */
    public static function createConnection(): ConnectionInterface
    {
        $dbConfig = [
            'host' => DB_HOST,
            'database' => DB_NAME,
            'username' => DB_USER,
            'password' => DB_PASS,
        ];

        return new MysqlConnection($dbConfig);
    }
    /**
     * creates and returns a new DAO instance, which uses a DatabaseService object
     * for data mapping and a QueryBuilder object for building SQL queries.
     * 
     * @return object
     */
    public function initialize(): object
    {
        $connection = self::createConnection();
        $queryBuilderFactory = new QueryBuilderFactory();
        $queryBuilder = $queryBuilderFactory->create($connection);
        $dataMapper = new DatabaseService($connection);
        $baseDAO = new DAO($dataMapper, $queryBuilder, $this->tableSchema, $this->tableSchemaId, $this->options);
        return $baseDAO;
    }
}