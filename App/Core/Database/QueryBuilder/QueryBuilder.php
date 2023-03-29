<?php

/**
 * This file is part of my Product Page project.
 *
 * @category  QueryBuilder
 * @package   App\Core\Database\QueryBuilder
 * @author    Victor Aurélio Rodrigues Ribeiro <victoraurelio_198@hotmail.com>
 * @copyright (c) 2023 Victor Aurelio
 * @link      https://github.com/VictorAurelio/product-page
 */

namespace App\Core\Database\QueryBuilder;

use App\Core\Database\QueryBuilder\Exception\QueryBuilderInvalidArgumentException;

/**
 * Abstract class that implements the QueryBuilderInterface.
 * Contains protected properties for building SQL queries,
 * as well as constants for defining SQL query types.
 */
abstract class QueryBuilder implements QueryBuilderInterface
{
    protected array $key = [];
    protected array $joins = [];
    protected string $sqlQuery = '';
    protected const SQL_DEFAULT = [
        'conditions' => [],
        'selectors' => [],
        'replace' => false,
        'distinct' => false,
        'from' => [],
        'where' => null,
        'and' => [],
        'or' => [],
        'orderby' => [],
        'fields' => [],
        'primary_key' => '',
        'table' => '',
        'type' => '',
        'raw' => '',
        'table_join' => '',
        'join_key' => '',
        'join' => []
    ];
    protected const QUERY_TYPES = ['insert', 'select', 'update', 'delete', 'raw', 'search', 'join'];
    /**
     * Summary of __construct
     */
    public function __construct()
    {
    }
    /**
     * Builds a query by merging the default SQL settings with the arguments passed
     * Throws a QueryBuilderInvalidArgumentException if the arguments
     * count is less than 0.
     * 
     * @param array $args
     * 
     * @throws QueryBuilderInvalidArgumentException
     * 
     * @return QueryBuilder
     */
    public function buildQuery(array $args = []): self
    {
        // var_dump($args);
        if (count($args) < 0) {
            throw new QueryBuilderInvalidArgumentException();
        }

        $arg = array_merge(self::SQL_DEFAULT, $args);
        $this->key = $arg;
        return $this;
    }
    /**
     * Validates if the given query type is valid.
     * 
     * @param string $type
     * 
     * @return bool
     */
    private function isQueryTypeValid(string $type): bool
    {
        if (in_array($type, self::QUERY_TYPES)) {
            return true;
        }
        return false;
    }
    /**
     * Generates and returns an insert query based on the query builder's settings.
     * 
     * @return string
     */
    public function insertQuery(): string
    {
        if (!$this->isQueryTypeValid('insert')) {
            return false;
        }

        if (!is_array($this->key['fields']) || count($this->key['fields']) === 0) {
            return false;
        }

        $index = array_keys($this->key['fields']);
        $value = array(implode(', ', $index), ":" . implode(', :', $index));

        $this->sqlQuery =
            "INSERT INTO {$this->key['table']} ({$value[0]}) VALUES({$value[1]})";

        return $this->sqlQuery;
    }
    /**
     * Generates and returns a select query based on the query builder's settings.
     * 
     * @return string
     */
    public function selectQuery(): string
    {
        if (!$this->isQueryTypeValid('select')) {
            return false;
        }

        $selectors = (!empty($this->key['selectors'])) ? implode(", ", $this->key['selectors']) : '*';
        $this->sqlQuery = "SELECT {$selectors} FROM {$this->key['table']}";
        $this->sqlQuery =  $this->hasConditions();
        $this->sqlQuery .= $this->orderByQuery();
        return $this->sqlQuery;
    }
    /**
     * Builds and returns an SQL UPDATE query string based on the provided key
     * fields array, which includes the table, fields and primary key, among
     * others. If successful, it returns the SQL query string, otherwise
     * it returns false.
     * 
     * @return string
     */
    public function updateQuery(): string
    {
        if (!$this->isQueryTypeValid('update')) {
            return false;
        }

        if (!is_array($this->key['fields']) || count($this->key['fields']) === 0) {
            return false;
        }

        $values = '';
        foreach ($this->key['fields'] as $field => $value) {
            if ($field !== $this->key['primary_key']) {
                $values .= $field . " = :" . $field . ", ";
            }
        }

        $values = substr_replace($values, '', -2);

        if (count($this->key['fields']) > 0) {
            $this->sqlQuery = "UPDATE {$this->key['table']} SET {$values} 
                WHERE id = :id LIMIT 1";
            if (
                isset($this->key['primary_key']) &&
                $this->key['primary_key'] === '0'
            ) {
                unset($this->key['primary_key']);
                $this->sqlQuery = "UPDATE {$this->key['table']} SET {$values}";
            }
        }

        return $this->sqlQuery;
    }
    /**
     * Builds and returns an SQL DELETE query string based on the provided key
     * fields array, which includes the table and conditions, among others.
     * If successful, it returns the SQL query string, otherwise it returns false.
     * 
     * @return string
     */
    public function deleteQuery(): string
    {
        if (!$this->isQueryTypeValid('delete')) {
            return false;
        }

        $conditions = $this->key['conditions'];
        $placeholders = implode(',', array_fill(0, count($conditions), '?'));
        $this->sqlQuery =
            "DELETE FROM {$this->key['table']}
        WHERE {$conditions[0]['field']}
            IN ({$placeholders})";

        return $this->sqlQuery;
    }
    /**
     * Builds and returns an SQL SELECT query string for searching records
     * in a table, based on the provided key fields array, which includes
     * the table, selectors and conditions, among others. If successful, it
     * returns the SQL query string, otherwise it returns false.
     * 
     * @return string
     */
    public function searchQuery(): string
    {
        if (!$this->isQueryTypeValid('search')) {
            return false;
        }

        if (is_array($this->key['selectors']) && $this->key['selectors'] != '') {
            $this->sqlQuery = "SELECT * FROM {$this->key['table']} WHERE ";
            if ($this->has('selectors')) {
                $values = [];
                foreach ($this->key['selectors'] as $selector) {
                    if ($this->key['isSearch'] === false) {
                        $values[] = $selector . " = " . ":{$selector}";
                    } else {
                        $values[] = $selector . " LIKE " . ":{$selector}";
                    }
                }
                if (count($this->key['selectors']) >= 1) {
                    $this->sqlQuery .= implode(" OR ", $values);
                }
            }
            $this->sqlQuery .= $this->orderByQuery();
            $this->sqlQuery .= $this->queryOffset();
        }
        return $this->sqlQuery ?? false;
    }
    /**
     * Generates a SQL query for exact search based on the
     * current query builder object.
     * 
     * @return string
     */
    public function searchQueryExact(): string
    {
        $this->key['isSearch'] = false;
        return $this->searchQuery();
    }
    /**
     * Executes an exact search query, returning a string with the generated SQL
     * statement. Checks if the query type is valid and if selectors exist in the
     * key array. If so, builds the query string using the selectors as fields and
     * their corresponding values as parameters. If there are multiple selectors,
     * the function generates an SQL statement with "AND" between them.
     * Also appends any ORDER BY and OFFSET clauses. Returns the
     * resulting SQL statement.
     * 
     * @return string
     */
    public function exactSearchQuery(): string
    {
        if (!$this->isQueryTypeValid('search')) {
            return false;
        }
        if (!is_array($this->key['selectors']) || empty($this->key['selectors'])) {
            return false;
        }
        $this->sqlQuery = "SELECT * FROM {$this->key['table']} WHERE ";
        $values = [];
        foreach ($this->key['selectors'] as $selector => $value) {
            $values[] = $selector . " = " . ":{$selector}";
        }
        if (count($this->key['selectors']) >= 1) {
            $this->sqlQuery .= implode(" AND ", $values);
        }
        $this->sqlQuery .= $this->orderByQuery();
        $this->sqlQuery .= $this->queryOffset();
        return $this->sqlQuery;
    }

    /**
     *  checks if a given key exists in the $key property of the object
     * 
     * @param string $key
     * 
     * @return bool
     */
    protected function has(string $key): bool
    {
        return isset($this->key[$key]);
    }
    /**
     * generates the SQL query for conditions, including joins,
     * conditions, and order by clauses
     * 
     * @return string
     */
    public function hasConditions(): string
    {
        foreach ($this->joins as $join) {
            $this->sqlQuery .= " " . $join;
        }

        if (!isset($this->key['conditions']) || empty($this->key['conditions'])) {
            return $this->sqlQuery;
        }

        if (!is_array($this->key['conditions'])) {
            return $this->sqlQuery;
        }

        $this->sqlQuery .= " WHERE " . implode(" AND ", $this->key['conditions']);
        $this->sqlQuery .= $this->orderByQuery();
        $this->sqlQuery .= $this->queryOffset();

        return $this->sqlQuery;
    }
    /**
     * Adds a limit statement to the SQL query if it has been set.
     * 
     * @return void
     */
    protected function queryLimit()
    {
        // Append the limit statement if set
        if (isset($this->key["params"]["limit"]) && $this->key["params"]["limit"] != "") {
            $this->sqlQuery .= " LIMIT " . $this->key["params"]["limit"] . " ";
        }
    }
    /**
     * Adds an orderby statement to the SQL query if it has been set.
     * 
     * @return void
     */
    protected function orderByQuery()
    {
        // Append the orderby statement if set
        if (isset($this->key["extras"]["orderby"]) && $this->key["extras"]["orderby"] != "") {
            $this->sqlQuery .= " ORDER BY " . $this->key["extras"]["orderby"] . " ";
        }
    }
    /**
     * Adds limit and offset statements to the SQL query for pagination.
     * 
     * @return void
     */
    protected function queryOffset()
    {
        // Append the limit and offset statement for adding pagination to the query
        if (isset($this->key["params"]["limit"]) && $this->key["params"]["offset"] != -1) {
            $this->sqlQuery .= " LIMIT :offset, :limit"; /* this is the short syntax */
        }
    }
    /**
     * adds an inner join clause to the query builder object
     * 
     * @param string $table
     * @param string $onCondition
     * 
     * @return QueryBuilder
     */
    public function innerJoin(string $table, string $onCondition): self
    {
        $this->joins[] = "INNER JOIN {$table} ON {$onCondition}";
        return $this;
    }

    /**
     * returns the raw SQL query string from the query builder object
     * 
     * @return string
     */
    public function rawQuery(): string
    {
        if (!$this->isQueryTypeValid('raw')) {
            return false;
        }

        $this->sqlQuery = $this->key['raw'];
        return $this->sqlQuery;
    }
}
