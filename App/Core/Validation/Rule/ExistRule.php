<?php

namespace App\Core\Validation\Rule;

use App\Core\Database\Connection\ConnectionInterface;

class ExistRule implements Rule
{
    protected ConnectionInterface $connection;
    protected string $table;
    protected string $column;

    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    public function validate(array $data, string $field, array $params)
    {
        $value = $data[$field] ?? null;
        $this->table = $params[0] ?? '';
        $this->column = $params[1] ?? '';
        $exceptValue = isset($params[2]) ? $params[2] : null;

        if (!$value || ($exceptValue !== null && $value === $exceptValue)) {
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
        $this->table = $params[0] ?? '';
        $this->column = $params[1] ?? '';
        return "{$field} already exists in the {$this->table} table and {$this->column} column";
    }
}
