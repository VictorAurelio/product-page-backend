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
     * Summary of create
     * 
     * @param DTOInterface $data
     * 
     * @return ?int
     */
    public function create(DTOInterface $data): ?int;
    /**
     * Summary of read
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
     * Summary of readWithOptions
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
     * Summary of update
     * 
     * @param DTOInterface $data
     * @param string       $primaryKey
     * 
     * @return bool
     */
    public function update(DTOInterface $data, string $primaryKey): bool;

    /**
     * Summary of delete
     * @param array $conditions
     * @return bool
     */
    public function delete(array $conditions): bool;
    /**
     * Summary of deleteByIds
     * 
     * @param array $ids
     * 
     * @return bool
     */
    public function deleteByIds(array $ids): bool;
    /**
     * Summary of rawQuery
     *
     * @param string       $rawQuery
     * @param DTOInterface $conditions
     * 
     * @return mixed
     */
    public function rawQuery(string $rawQuery, DTOInterface $conditions): mixed;
}
