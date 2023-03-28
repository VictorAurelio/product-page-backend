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
 * Summary of QueryBuilder
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
     * Main constructor class
     * @return void
     * 
     */
    public function __construct()
    {
    }
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
    private function isQueryTypeValid(string $type): bool
    {
        if (in_array($type, self::QUERY_TYPES)) {
            return true;
        }
        return false;
    }

    public function insertQuery(): string
    {
        if ($this->isQueryTypeValid('insert')) {
            if (is_array($this->key['fields']) && count($this->key['fields']) > 0) {
                $index = array_keys($this->key['fields']);
                $value = array(implode(', ', $index), ":" . implode(', :', $index));
                $this->sqlQuery = "INSERT INTO {$this->key['table']} ({$value[0]}) VALUES({$value[1]})";
                return $this->sqlQuery;
            }
        }
        return false;
    }

    public function selectQuery(): string
    {
        if ($this->isQueryTypeValid('select')) {
            $selectors = (!empty($this->key['selectors'])) ? implode(", ", $this->key['selectors']) : '*';
            $this->sqlQuery = "SELECT {$selectors} FROM {$this->key['table']}";
            $this->sqlQuery =  $this->hasConditions();
            $this->sqlQuery .= $this->orderByQuery();
            return $this->sqlQuery;
        }
        return false;
    }

    public function updateQuery(): string
    {
        $values = '';
        if ($this->isQueryTypeValid('update')) {
            if (is_array($this->key['fields']) && count($this->key['fields']) > 0) {
                foreach ($this->key['fields'] as $field => $value) {
                    if ($field !== $this->key['primary_key']) {
                        $values .= $field . " = :" . $field . ", ";
                    }
                }

                $values = substr_replace($values, '', -2);

                if (count($this->key['fields']) > 0) {
                    $this->sqlQuery = "UPDATE {$this->key['table']} SET {$values} 
                        WHERE id = :id LIMIT 1";
                    if (isset($this->key['primary_key']) && $this->key['primary_key'] === '0') {
                        unset($this->key['primary_key']);
                        $this->sqlQuery = "UPDATE {$this->key['table']} SET {$values}";
                    }
                }
                // echo'<br><br>';var_dump($this->sqlQuery);echo'<br><br>';
                return $this->sqlQuery;
            }
        }
        return false;
    }
    public function deleteQuery(): string
    {
        if ($this->isQueryTypeValid('delete')) {
            $conditions = $this->key['conditions'];
            $placeholders = implode(',', array_fill(0, count($conditions), '?'));
            $this->sqlQuery = "DELETE FROM {$this->key['table']} WHERE {$conditions[0]['field']} IN ({$placeholders})";
            return $this->sqlQuery;
        }
        return false;
    }
    /**
     * Summary of searchQuery
     * @return string
     */
    public function searchQuery(): string
    {
        if ($this->isQueryTypeValid('search')) {
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
            return $this->sqlQuery;
        }
    }
    public function searchQueryExact(): string
    {
        $this->key['isSearch'] = false;
        return $this->searchQuery();
    }
    public function exactSearchQuery(): string
    {
        if ($this->isQueryTypeValid('search')) {
            if (is_array($this->key['selectors']) && $this->key['selectors'] != '') {
                $this->sqlQuery = "SELECT * FROM {$this->key['table']} WHERE ";
                if ($this->has('selectors')) {
                    $values = [];
                    foreach ($this->key['selectors'] as $selector => $value) {
                        $values[] = $selector . " = " . ":{$selector}";
                    }
                    if (count($this->key['selectors']) >= 1) {
                        $this->sqlQuery .= implode(" AND ", $values);
                    }
                }
                $this->sqlQuery .= $this->orderByQuery();
                $this->sqlQuery .= $this->queryOffset();
            }
            return $this->sqlQuery;
        }
    }
    protected function has(string $key): bool
    {
        return isset($this->key[$key]);
    }
    public function hasConditions()
    {
        foreach ($this->joins as $join) {
            $this->sqlQuery .= " " . $join;
        }

        if (isset($this->key['conditions']) && !empty($this->key['conditions'])) {
            if (is_array($this->key['conditions'])) {
                $this->sqlQuery .= " WHERE " . implode(" AND ", $this->key['conditions']);
            }
        }

        $this->sqlQuery .= $this->orderByQuery();
        $this->sqlQuery .= $this->queryOffset();

        return $this->sqlQuery;
    }
    protected function queryLimit()
    {
        // Append the limit statement if set
        if (isset($this->key["params"]["limit"]) && $this->key["params"]["limit"] != "") {
            $this->sqlQuery .= " LIMIT " . $this->key["params"]["limit"] . " ";
        }
    }
    protected function orderByQuery()
    {
        // Append the orderby statement if set
        if (isset($this->key["extras"]["orderby"]) && $this->key["extras"]["orderby"] != "") {
            $this->sqlQuery .= " ORDER BY " . $this->key["extras"]["orderby"] . " ";
        }
    }
    protected function queryOffset()
    {
        // Append the limit and offset statement for adding pagination to the query
        if (isset($this->key["params"]["limit"]) && $this->key["params"]["offset"] != -1) {
            $this->sqlQuery .= " LIMIT :offset, :limit"; /* this is the short syntax */
        }
    }
    public function innerJoin(string $table, string $onCondition): self
    {
        $this->joins[] = "INNER JOIN {$table} ON {$onCondition}";
        return $this;
    }

    public function rawQuery(): string
    {
        if ($this->isQueryTypeValid('raw')) {
            $this->sqlQuery = $this->key['raw'];
            return $this->sqlQuery;
        }
    }
}
