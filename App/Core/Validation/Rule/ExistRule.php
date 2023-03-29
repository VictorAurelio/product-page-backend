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

/**
 * The ExistRule class implements the Rule interface and checks if a given value
 * for a specific column already exists in a database table.
 */
class ExistRule implements Rule
{
    protected ConnectionInterface $connection;
    /**
     * Summary of table
     * 
     * @var string
     */
    protected string $table;
    /**
     * Summary of column
     * @var string
     */
    protected string $column;

    /**
     * Takes a ConnectionInterface object as a parameter
     * 
     * @param ConnectionInterface $connection
     */
    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Validates if a value already exists in a given column of a given table.
     * Takes an array of data, a string field and an array of parameters as
     * parameters. Returns true if the value does not exist in the table, or
     * if the value is null or if the value is the same as the exceptValue
     * parameter, otherwise returns false.
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
        $this->table = $params[0] ?? '';
        $this->column = $params[1] ?? '';
        $exceptValue = isset($params[2]) ? $params[2] : null;

        if (!$value || ($exceptValue !== null && $value === $exceptValue)) {
            return true;
        }

        $query = "SELECT COUNT(*) as count
                    FROM {$this->table}
                    WHERE {$this->column} = :value";
        $statement = $this->connection->pdo()->prepare($query);
        $statement->execute(['value' => $value]);
        $result = $statement->fetch();

        return ($result['count'] == 0);
    }

    /**
     * Returns an error message string if the value already exists in the table.
     * Takes an array of data, a string field and an array of parameters
     * as parameters. Returns the error message string.
     * 
     * @param array $data
     * @param string $field
     * @param array $params
     * 
     * @return string
     */
    public function getMessage(
        array $data,
        string $field,
        array $params
    ) {
        $this->table = $params[0] ?? '';
        $this->column = $params[1] ?? '';

        return json_encode(
            [
                "message" => "{$field} already exists in the {$this->table}
                                table and {$this->column} column"
            ]
        );
    }
}
