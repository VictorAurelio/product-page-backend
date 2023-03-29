<?php

/**
 * This file is part of my Product Page project.
 *
 * @category  Interface
 * @package   App\Core\Database\DatabaseService
 * @author    Victor Aurélio Rodrigues Ribeiro <victoraurelio_198@hotmail.com>
 * @copyright (c) 2023 Victor Aurelio
 * @link      https://github.com/VictorAurelio/product-page
 */

namespace App\Core\Database\DatabaseService;

/**
 * DatabaseServiceInterface which defines the methods for a database service
 */
interface DatabaseServiceInterface
{
    /**
     * Prepare the query string
     * 
     * @param string $sqlQuery
     * 
     * @return self
     */
    public function prepare(string $sqlQuery): self;

    /**
     * Explicit data type for the parameter using the PDO::PARAM_* constants.
     * 
     * @param mixed $value
     * 
     * @return mixed
     */
    public function bind($value);

    /**
     * binds the parameters to the query string.
     * 
     * @param array $fields
     * @param bool $isSearch
     * 
     * @return mixed
     */
    public function bindParameters(array $fields, bool $isSearch = false): self;

    /**
     * returns the number of rows affected by a DELETE, INSERT, or UPDATE statement.
     * 
     * @return int|null
     */
    public function numRows(): int;

    /**
     * executes a prepared statement.
     * 
     * @return void
     */
    public function execute();


    /**
     * returns a single database row as an object.
     * 
     * @return object|null
     */
    public function result(): ?Object;

    /**
     * Returns all the rows within the database as an array
     * 
     * @return array
     */
    public function results(): array;

    /**
     * Returns the last inserted row ID from database table
     * 
     * @throws Throwable
     * 
     * @return int
     */
    public function getLastId(): int;
}
