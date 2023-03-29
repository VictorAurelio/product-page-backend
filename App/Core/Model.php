<?php

/**
 * This file is part of my Product Page project.
 *
 * @category  Model
 * @package   App\Core
 * @author    Victor Aurélio Rodrigues Ribeiro <victoraurelio_198@hotmail.com>
 * @copyright (c) 2023 Victor Aurelio
 * @link      https://github.com/VictorAurelio/product-page
 */

namespace App\Core;

use App\Core\Database\DatabaseService\DatabaseService;
use App\Core\Database\QueryBuilder\MysqlQueryBuilder;
use App\Core\Database\Connection\ConnectionInterface;
use App\Core\Exceptions\AppInvalidArgumentException;
use App\Core\Database\DatabaseFactory;
use App\Core\Database\DAO\DAO;

/**
 * This is the Model class, which represents a base class for
 * creating models in the application.
 */
class Model
{
    /**
     * an instance of ConnectionInterface that represents the
     * database connection used by the model.
     * 
     * @var ConnectionInterface
     */
    protected ConnectionInterface $connection;
    /**
     * a string that represents the name of the primary key of the table
     * associated with the model.
     * @var string
     */
    protected string $tableSchemaId;
    /**
     * a string that represents the name of the table associated with the model.
     * 
     * @var string
     */
    protected string $tableSchema;
    /**
     * an instance of DAO that provides a data access layer to interact with the
     * table associated with the model.
     * 
     * @var DAO
     */
    protected DAO $dao;
    /**
     * Constructor method for the Model class. Initializes the tableSchema,
     * tableSchemaId, and connection properties, and creates a new instance
     * of the DAO class with the given arguments.
     * 
     * @param string $tableSchema
     * @param string $tableSchemaId
     * @param ConnectionInterface $connection
     * 
     * @throws AppInvalidArgumentException
     */
    public function __construct(
        string $tableSchema,
        string $tableSchemaId,
        ConnectionInterface $connection
    ) {
        if (empty($tableSchema || empty($tableSchemaId))) {
            throw new AppInvalidArgumentException(
                'These arguments are required.'
            );
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
    /**
     * Method that creates a new instance of the DatabaseFactory class and
     * initializes the database, returning the result as an object.
     * 
     * @return object
     */
    public function initialize(): object
    {
        $options = [];
        $handler = new DatabaseFactory($this->connection, $this->tableSchema, $this->tableSchemaId, $options);
        return $handler->initialize();
    }
}
