<?php

/**
 * This file is part of my Product Page project.
 *
 * @category  Validation rules
 * @package   App\Core\Validation\Rule
 * @author    Victor Aurélio Rodrigues Ribeiro <victoraurelio_198@hotmail.com>
 * @copyright (c) 2023 Victor Aurelio
 * @link      https://github.com/VictorAurelio/product-page
 */

namespace App\Core\Validation\Rule;

use App\Core\Database\Connection\ConnectionInterface;
use App\Core\Validation\Rule\Rule;

class UniqueRule implements Rule
{
    protected ConnectionInterface $connection;
    protected string $table;
    protected string $column;

    public function __construct(ConnectionInterface $connection, string $table, string $column)
    {
        $this->connection = $connection;
        $this->table = $table;
        $this->column = $column;
    }

    public function validate(array $data, string $field, array $params)
    {
        $value = $data[$field] ?? null;

        if (!$value) {
            return true;
        }

        $query = "SELECT COUNT(*) as count FROM {$this->table} WHERE {$this->column} = :value";
        $statement = $this->connection->pdo()->prepare($query);
        $statement->execute(['value' => $value]);
        $result = $statement->fetch();

        return ($result['count'] == 0);
    }

    public function getMessage(array $data, string $field, array $params)
    {
        return json_encode(["message" => "{$field} already exists in the database"]);
    }
}