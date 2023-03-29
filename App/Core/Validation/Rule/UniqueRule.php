<?php

/**
 * This file is part of my Product Page project.
 *
 * @category  Validation rule
 * @package   App\Core\Validation\Rule
 * @author    Victor Aurélio Rodrigues Ribeiro <victoraurelio_198@hotmail.com>
 * @copyright (c) 2023 Victor Aurelio
 * @link      https://github.com/VictorAurelio/product-page
 */

namespace App\Core\Validation\Rule;

use App\Core\Database\Connection\ConnectionInterface;
use App\Core\Validation\Rule\Rule;

/**
 * The UniqueRule class is responsible for validating whether a given value
 * already exists in a database table column.
 */
class UniqueRule implements Rule
{
    /**
     * property holds the database connection
     * 
     * @var ConnectionInterface
     */
    protected ConnectionInterface $connection;
    /**
     * hold the table name to be checked
     * 
     * @var string
     */
    protected string $table;
    /**
     * hold the column name to be checked
     * 
     * @var string
     */
    protected string $column;

    /**
     * The values of $table and $column are set in the constructor
     * method, which takes these two values as parameters and also receives the
     * connection.
     * 
     * @param ConnectionInterface $connection
     * @param string $table
     * @param string $column
     */
    public function __construct(ConnectionInterface $connection, string $table, string $column)
    {
        $this->connection = $connection;
        $this->table = $table;
        $this->column = $column;
    }
    /**
     * method checks whether the specified field value already exists
     * in the specified database table column
     * 
     * @param array $data
     * @param string $field
     * @param array $params
     * 
     * @return bool
     */
    public function validate(array $data, string $field, array $params)
    {
        $value = $data[$field] ?? null;

        if (!$value) {
            return true;
        }

        $query = "SELECT COUNT(*) as count
                    FROM {$this->table} WHERE {$this->column} = :value";
        $statement = $this->connection->pdo()->prepare($query);
        $statement->execute(['value' => $value]);
        $result = $statement->fetch();

        return ($result['count'] == 0);
    }
    /**
     * returns an error message if the validation fails
     * 
     * @param array $data
     * @param string $field
     * @param array $params
     * 
     * @return bool|string
     */
    public function getMessage(array $data, string $field, array $params)
    {
        return json_encode(
            [
                "message" => "{$field} already exists in the database"
            ]
        );
    }
}
