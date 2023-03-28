<?php

/**
 * This file is part of my Product Page project.
 *
 * @category  Database
 * @package   App\Core\Database
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
 * Summary of DatabaseService
 */
class DatabaseService implements DatabaseServiceInterface
{
    protected Connection $connection;
    private PDOStatement $_statement;
    /**
     * Summary of __construct
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }
    /**
     * Summary of isEmpty
     * @param string|null $errorMessage
     * @throws DatabaseServiceException
     * @param mixed $value
     * @return void
     */
    private function isEmpty($value, string $errorMessage = null)
    {
        if (empty($value)) {
            throw new DatabaseServiceException($errorMessage);
        }
    }
    /**
     * Summary of isArray
     * @throws DatabaseServiceException
     * @param array $value
     * @return void
     */
    private function isArray(array $value)
    {
        if (!is_array($value)) {
            throw new DatabaseServiceException('Your argument needs to be an array');
        }
    }
    /**
     * Summary of prepare
     * @param string $sqlQuery
     * @return DatabaseService
     */
    public function prepare(string $sqlQuery): self
    {
        $this->_statement = $this->connection->pdo()->prepare($sqlQuery);
        return $this;
    }
    /**
     * Summary of bind
     * @param mixed $value
     * @return mixed
     */
    public function bind($value): mixed
    {
        try {
            switch ($value) {
                case is_bool($value):
                case intval($value);
                    $dataType = PDO::PARAM_INT;
                    break;
                case is_null($value):
                    $dataType = PDO::PARAM_NULL;
                    break;
                default:
                    $dataType = PDO::PARAM_STR;
                    break;
            }
            return $dataType;
        } catch (DatabaseServiceException $exception) {
            throw $exception;
        }
    }
    /**
     * @inheritDoc
     *
     * @param array $fields
     * @param boolean $isSearch
     * @return self
     */
    public function bindParameters(array $fields, bool $isSearch = false): self
    {
        $this->isArray($fields);
        if (is_array($fields)) {
            $type = ($isSearch === false) ? $this->bindValues($fields) : $this->bindSearchValues($fields);
            if ($type) {
                return $this;
            }
        }
    }
    protected function bindMassDeleteParameters(array $parameters): PDOStatement
    {
        $paramIndex = 1;
        foreach ($parameters as $value) {
            $this->_statement->bindValue($paramIndex, $value, $this->bind($value));
            $paramIndex++;
        }
        return $this->_statement;
    }

    /**
     * Summary of bindValues
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
            $this->_statement->bindValue($key, $value, $this->bind($value));
        }
        return $this->_statement;
    }

    /**
     * Summary of bindSearchValues
     * 
     * @param array $fields
     * 
     * @return PDOStatement
     */
    protected function bindSearchValues(array $fields): PDOStatement
    {
        $this->isArray($fields); // don't need
        foreach ($fields as $key => $value) {
            $this->_statement->bindValue(':' . $key,  '%' . $value . '%', $this->bind($value));
        }
        return $this->_statement;
    }

    /**
     * Summary of execute
     * 
     * @return bool
     */
    public function execute()
    {
        if ($this->_statement)
            return $this->_statement->execute();
    }
    /**
     * Summary of numRows
     * 
     * @return integer
     */
    public function numRows(): int
    {
        if ($this->_statement)
            return $this->_statement->rowCount();
    }

    /**
     * Summary of result
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
     * @inheritDoc
     * @return array
     */
    public function results(): array
    {
        if ($this->_statement)
            return $this->_statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @inheritDoc
     * @throws Throwable
     */
    public function getLastId(): int
    {
        try {
            if ($this->connection->pdo()) {
                $lastID = $this->connection->pdo()->lastInsertId();
                if (!empty($lastID)) {
                    return intval($lastID);
                }
            }
        } catch (Throwable $throwable) {
            throw $throwable;
        }
    }
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
    public function buildInsertQueryParameters(array $conditions = [], array $parameters = []): array
    {
        return (!empty($parameters) || (!empty($conditions)) ? array_merge($conditions, $parameters) : $parameters);
    }
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
                        $allParameters[':' . $paramName] = $parameters[':' . $paramName];
                    }
                }
            }
        }

        return $allParameters;
    }

    /**
     * Summary of persist
     * @param string $sqlQuery
     * @param array $parameters
     * @param bool $search
     * @param bool $isMassDelete
     * @throws PDOException
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
