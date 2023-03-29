<?php

/**
 * This file is part of my Product Page project.
 *
 * @category  Interface
 * @package   App\Core\Database\DAO
 * @author    Victor Aurélio Rodrigues Ribeiro <victoraurelio_198@hotmail.com>
 * @copyright (c) 2023 Victor Aurelio
 * @link      https://github.com/VictorAurelio/product-page
 */

namespace App\Core\Database\DAO;

use App\DTO\DTOInterface;

/**
 * ConnectionInterface defines the contract for DAO class.
 */
interface DAOInterface
{
    /**
     * creates a new record in the database using a DTO, returns the ID of the
     * newly created record or null if there was an error
     * 
     * @param DTOInterface $data
     * 
     * @return ?int
     */
    public function create(DTOInterface $data): ?int;
    /**
     * retrieves data from the database based on specified conditions
     * and parameters, returns an array of matching records
     * 
     * @param array $selectors
     * @param array $conditions
     * @param array $parameters
     * @param array $optional
     * 
     * @return array
     */
    public function read(
        array $selectors = [],
        array $conditions = [],
        array $parameters = [],
        array $optional = []
    ): array;
    /**
     * retrieves data from the database based on specified selectors, conditions,
     * and parameters, performs a join operation on two tables, and returns
     * an array of matching records
     * 
     * @param array $selectors
     * @param array $conditions
     * @param array $parameters
     * @param array $optional
     * 
     * @return array
     */
    public function readWithOptions(
        array $selectors = [],
        array $conditions = [],
        array $parameters = []
    ): array;
    /**
     * updates a record in the database using a DTO and a primary key value,
     * returns a boolean indicating whether the update was successful or not
     * 
     * @param DTOInterface $data
     * @param string       $primaryKey
     * 
     * @return bool
     */
    public function update(DTOInterface $data, string $primaryKey): bool;

    /**
     * deletes one or more records from the database based on specified conditions,
     * returns a boolean indicating whether the deletion was successful or not
     * 
     * @param array $conditions
     * 
     * @return bool
     */
    public function delete(array $conditions): bool;
    /**
     * Retrieves the IDs passed by the controllers to enable the mass delete
     * 
     * @param array $ids
     * 
     * @return bool
     */
    public function deleteByIds(array $ids): bool;
    /**
     * executes a raw SQL query with specified conditions,
     * returns the results of the query
     *
     * @param string       $rawQuery
     * @param DTOInterface $conditions
     * 
     * @return mixed
     */
    public function rawQuery(string $rawQuery, DTOInterface $conditions): mixed;
}
