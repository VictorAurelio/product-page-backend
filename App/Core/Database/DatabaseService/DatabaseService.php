<?php

/**
 * This file is part of my Product Page project.
 *
 * @category  DatabaseService
 * @package   App\Core\Database\DatabaseService
 * @author    Victor Aurélio Rodrigues Ribeiro <victoraurelio_198@hotmail.com>
 * @copyright (c) 2023 Victor Aurelio
 * @link      https://github.com/VictorAurelio/product-page
 */

namespace App\Core\Database\DatabaseService;

use App\Core\Database\DatabaseService\Exception\DatabaseServiceException;
use App\Core\Database\Connection\Connection;
use PDOStatement;
use PDOException;
use Throwable;
use PDO;

/**
 * The DatabaseService class is a service layer that provides methods to execute
 * database operations using PDO.
 */
class DatabaseService implements DatabaseServiceInterface
{
    /**
     * an instance of the Connection class
     * 
     * @var Connection
     */
    protected Connection $connection;
    /**
     * a private property of type PDOStatement used to store the prepared
     * statement of a query.
     * 
     * @var PDOStatement
     */
    private PDOStatement $_statement;
    /**
     * Constructor that receives a Connection object and initializes
     * the DatabaseService.
     * 
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }
    /**
     * Private method that throws a DatabaseServiceException if the value passed
     * as argument is empty.
     * 
     * @param string|null $errorMessage
     * @param mixed $value
     * 
     * @throws DatabaseServiceException
     * 
     * @return void
     */
    private function isEmpty($value, string $errorMessage = null)
    {
        if (empty($value)) {
            throw new DatabaseServiceException($errorMessage);
        }
    }
    /**
     * Private method that throws a DatabaseServiceException if the value passed
     * as argument is not an array.
     * 
     * @param array $value
     * 
     * @throws DatabaseServiceException
     * 
     * @return void
     */
    private function isArray(array $value)
    {
        if (!is_array($value)) {
            throw new DatabaseServiceException(
                'Your argument needs to be an array'
            );
        }
    }
    /**
     * Method that prepares the SQL query for execution and returns
     * the DatabaseService.
     * 
     * @param string $sqlQuery
     * 
     * @return DatabaseService
     */
    public function prepare(string $sqlQuery): self
    {
        $this->_statement = $this->connection->pdo()->prepare($sqlQuery);
        return $this;
    }
    /**
     * Method that binds a value to a parameter and returns the data type
     * of the value.
     * 
     * @param mixed $value
     * 
     * @return mixed
     */
    public function bind($value): mixed
    {
        if (is_bool($value) || is_int($value)) {
            return PDO::PARAM_INT;
        }

        if (is_null($value)) {
            return PDO::PARAM_NULL;
        }

        if (is_string($value)) {
            return PDO::PARAM_STR;
        }

        throw new DatabaseServiceException(
            'Unknown data type for binding parameter'
        );
    }
    /**
     * Method that binds an array of parameters to the SQL query and returns
     * the DatabaseService.
     * 
     * @inheritDoc
     *
     * @param array $fields
     * @param boolean $isSearch
     * 
     * @return self
     */
    public function bindParameters(array $fields, bool $isSearch = false): self
    {
        $this->isArray($fields);

        if (!is_array($fields)) {
            return $this;
        }

        $type = ($isSearch === false) ?
            $this->bindValues($fields) :
            $this->bindSearchValues($fields);

        if (!$type) {
            return $this;
        }

        return $this;
    }
    /**
     * Protected method that binds multiple parameters to the SQL query
     * for mass deletion.
     * 
     * @param array $parameters
     * 
     * @return PDOStatement
     */
    protected function bindMassDeleteParameters(array $parameters): PDOStatement
    {
        $paramIndex = 1;
        foreach ($parameters as $value) {
            $this->_statement
                ->bindValue(
                    $paramIndex,
                    $value,
                    $this->bind($value)
                );
            $paramIndex++;
        }
        return $this->_statement;
    }

    /**
     * Protected method that binds parameter values to the SQL query
     * for a regular search.
     * 
     * @param array $fields
     * 
     * @return PDOStatement
     */
    protected function bindValues(array $fields): PDOStatement
    {
        $this->isArray($fields); // don't need
        foreach ($fields as $key => $value) {
            if (strpos($key, ':') !== 0) {
                $key = ':' . $key;
            }
            $this->_statement
                ->bindValue(
                    $key,
                    $value,
                    $this->bind($value)
                );
        }
        return $this->_statement;
    }

    /**
     * Protected method that binds parameter values to the SQL query
     * for a search operation.
     * 
     * @param array $fields
     * 
     * @return PDOStatement
     */
    protected function bindSearchValues(array $fields): PDOStatement
    {
        $this->isArray($fields); // don't need
        foreach ($fields as $key => $value) {
            $this->_statement
                ->bindValue(
                    ':' . $key,
                    '%' . $value . '%',
                    $this->bind($value)
                );
        }
        return $this->_statement;
    }

    /**
     * Method that executes the prepared SQL query.
     * 
     * @return bool
     */
    public function execute()
    {
        if ($this->_statement)
            return $this->_statement->execute();
    }
    /**
     * Method that returns the number of rows affected by
     * the last SQL query executed.
     * 
     * @return integer
     */
    public function numRows(): int
    {
        if ($this->_statement)
            return $this->_statement->rowCount();
    }

    /**
     * Method that returns the first row of the result set as an object.
     * 
     * @return object|null
     */
    public function result(): ?Object
    {
        if ($this->_statement) {
            $result = $this->_statement->fetch(PDO::FETCH_OBJ);
            return $result === false ? null : $result;
        }
    }

    /**
     * Method that returns the entire result set as an array.
     * 
     * @inheritDoc
     * 
     * @return array
     */
    public function results(): array
    {
        if ($this->_statement)
            return $this->_statement->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * 
     * Method that returns the last inserted ID.
     * 
     * @return int
     */
    public function getLastId(): int
    {
        if (!$this->connection->pdo()) {
            return 0;
        }

        try {
            $lastID = $this->connection->pdo()->lastInsertId();
            if (!empty($lastID)) {
                return intval($lastID);
            }
        } catch (Throwable $throwable) {
            throw $throwable;
        }

        return 0;
    }
    /**
     * Method that extracts values from an array of conditions
     * to build a DELETE query.
     * 
     * @param array $conditions
     * 
     * @return array
     */
    public function buildDeleteQueryParameters(array $conditions = []): array
    {
        $parameters = [];
        foreach ($conditions as $condition) {
            if (isset($condition['value'])) {
                $parameters[] = $condition['value'];
            }
        }
        return $parameters;
    }
    /**
     * Method that extracts values from an array of fields
     * to build an UPDATE query.
     * 
     * @param array $fields
     * 
     * @return array
     */
    public function buildUpdateQueryParameters(array $fields = []): array
    {
        $parameters = [];
        foreach ($fields as $key => $value) {
            if (strpos($key, '_') === 0) {
                $key = substr($key, 1);
            }
            $parameters[":$key"] = $value;
        }
        $parameters[':id'] = $fields['id'];
        return $parameters;
    }
    /**
     * Method that merges two arrays of parameters
     * to build an INSERT query.
     * 
     * @param array $conditions
     * @param array $parameters
     * 
     * @return array
     */
    public function buildInsertQueryParameters(array $conditions = [], array $parameters = []): array
    {
        return (!empty($parameters) || (!empty($conditions)) ? array_merge($conditions, $parameters) : $parameters);
    }
    /**
     * Method that extracts values from an array of conditions to build a query.
     * Will likely be used mostly for read/search.
     * 
     * @param array $conditions
     * @param array $parameters
     * 
     * @return array
     */
    public function buildQueryParameters(array $conditions = [], array $parameters = []): array
    {
        if (empty($conditions)) {
            return $parameters;
        }

        $allParameters = [];
        foreach ($conditions as $condition) {
            if (preg_match_all('/:(\w+)/', $condition, $matches)) {
                foreach ($matches[1] as $paramName) {
                    if (isset($parameters[':' . $paramName])) {

                        $allParameters[':' . $paramName]
                            = $parameters[':' . $paramName];
                    }
                }
            }
        }
        return $allParameters;
    }

    /**
     * Method that executes a SQL query with the given parameters and throws
     * a PDOException if there is an error.
     * 
     * @param string $sqlQuery
     * @param array $parameters
     * @param bool $search
     * @param bool $isMassDelete
     * 
     * @throws PDOException
     * 
     * @return void
     */
    public function persist(string $sqlQuery, array $parameters, bool $search = false, bool $isMassDelete = false): void
    {
        try {
            $prepared = $this->prepare($sqlQuery);
            if ($isMassDelete) {
                $prepared->bindMassDeleteParameters($parameters);
            } else {
                $prepared->bindParameters($parameters, $search);
            }
            $prepared->execute();
        } catch (PDOException $e) {
            throw new PDOException('Data persistent error ' . $e->getMessage());
        }
    }
}
