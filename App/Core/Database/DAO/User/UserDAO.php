<?php

/**
 * This file is part of my Product Page project.
 *
 * @category  DAO
 * @package   App\Core\Database\DAO\User
 * @author    Victor Aurélio Rodrigues Ribeiro <victoraurelio_198@hotmail.com>
 * @copyright (c) 2023 Victor Aurelio
 * @link      https://github.com/VictorAurelio/product-page
 */

namespace App\Core\Database\DAO\User;

use App\Core\Database\DAO\DAOInterface;
use App\Core\Database\DAO\DAO;
use InvalidArgumentException;
use App\DTO\DTOInterface;
use App\DTO\User\UserDTO;
use Throwable;

/**
 * Summary of UserDAO
 */
class UserDAO implements DAOInterface
{
    /**
     * Summary of dao
     *
     * @var DAO
     */
    protected DAO $dao;

    /**
     * Summary of __construct
     * 
     * @param DAO $dao
     */
    public function __construct(DAO $dao)
    {
        $this->dao = $dao;
    }
    /**
     * Summary of create
     * 
     * @param DTOInterface $data
     * 
     * @throws InvalidArgumentException
     * 
     * @return ?int
     */
    public function create(DTOInterface $data): ?int
    {
        if (!$data instanceof UserDTO) {
            throw new InvalidArgumentException('Expected UserDTO instance.');
        }
        try {
            // Convert UserDTO to array
            $fields = $data->toArray();

            $args = [
                'table' => $this->dao->getSchema(),
                'type' => 'insert',
                'fields' => $fields
            ];
            $query = $this->dao->getQueryBuilder()->buildQuery($args)->insertQuery();
            $this->dao->getDataMapper()->persist(
                $query,
                $this->dao->getDataMapper()->buildInsertQueryParameters($fields)
            );

            if ($this->dao->getDataMapper()->numRows() == 1) {
                return $this->dao->lastID();
            }
        } catch (Throwable $throwable) {
            throw $throwable;
        }
        return 0;
    }
    /**
     * Summary of read
     *
     * @param  array $selectors
     * @param  array $conditions
     * @param  array $parameters
     * @param  array $optional
     * @return array
     */
    public function read(
        array $selectors = [],
        array $conditions = [],
        array $parameters = [],
        array $optional = []
    ): array {
        try {
            $args = [
                'table' => $this->dao->getSchema(),
                'type' => 'select',
                'selectors' => $selectors,
                'conditions' => $conditions,
                'params' => $parameters,
                'extras' => $optional
            ];
            $query = $this->dao->getQueryBuilder()->buildQuery($args)->selectQuery();
            $this->dao
                ->getDataMapper()
                ->persist(
                    $query,
                    $this->dao
                        ->getDataMapper()
                        ->buildQueryParameters($conditions, $parameters)
                );
            if ($this->dao->getDataMapper()->numRows() > 0) {
                return $this->dao->getDataMapper()->results();
            }
        } catch (Throwable $e) {
            return $e->getMessage();
        }
        return ['no data'];
    }
    /**
     * Summary of update
     * 
     * @param DTOInterface $data
     * @param string       $primaryKey
     * 
     * @throws InvalidArgumentException
     * 
     * @return bool
     */
    public function update(DTOInterface $data, string $primaryKey): bool
    {
        if (!$data instanceof UserDTO) {
            throw new InvalidArgumentException('Expected UserDTO instance.');
        }

        $fields = $data->toArray();

        try {
            $args = [
                'table' => $this->dao->getSchema(),
                'type' => 'update',
                'fields' => $fields,
                'primary_key' => $primaryKey
            ];
            $query = $this->dao->getQueryBuilder()->buildQuery($args)->updateQuery();
            $this->dao->getDataMapper()->persist(
                $query,
                $this->dao->getDataMapper()->buildQueryParameters($fields)
            );
            if ($this->dao->getDataMapper()->numRows() === 1) {
                return true;
            }
        } catch (Throwable $throwable) {
            throw $throwable;
        }

        return false;
    }
    /**
     * Summary of delete
     * 
     * @param DTOInterface $conditions
     * 
     * @throws InvalidArgumentException
     * 
     * @return bool
     */
    public function delete(array $conditions): bool
    {
        $conditionArray = $conditions->toArray();

        try {
            $args = [
                'table' => $this->dao->getSchema(),
                'type' => 'delete',
                'conditions' => $conditionArray
            ];
            $query = $this->dao->getQueryBuilder()->buildQuery($args)->deleteQuery();
            $this->dao->getDataMapper()->persist(
                $query,
                $this->dao->getDataMapper()->buildQueryParameters($conditionArray)
            );
            if ($this->dao->getDataMapper()->numRows() === 1) {
                return true;
            }
        } catch (Throwable $throwable) {
            throw $throwable;
        }

        return false;
    }
    /**
     * Summary of findByEmail
     * 
     * @param UserDTO $fields
     * 
     * @throws InvalidArgumentException
     * 
     * @return UserDTO|null
     */
    public function findByEmail(UserDTO $fields): ?UserDTO
    {
        if (!$fields instanceof UserDTO) {
            throw new InvalidArgumentException('Expected UserDTO instance.');
        }

        $fieldsArray = $fields->toArray();

        $sqlQuery = $this->dao->getQueryBuilder()->buildQuery(
            [
                'type' => 'search',
                'selectors' => ['email' => $fieldsArray['email']],
                'table' => $this->dao->getSchema()
            ]
        )->exactSearchQuery();

        $this->dao->getDataMapper()->persist(
            $sqlQuery,
            $this->dao
                ->getDataMapper()
                ->buildQueryParameters([], ['email' => $fieldsArray['email']]),
            false
        );
        $result = $this->dao->getDataMapper()->result();

        if ($result === null) {
            return null;
        }

        $userDTO = new UserDTO();
        $userDTO->setId($result->id);
        $userDTO->setName($result->name);
        $userDTO->setEmail($result->email);
        $userDTO->setPassword($result->password);

        return $userDTO;
    }
    /**
     * Summary of rawQuery
     * 
     * @param string       $rawQuery
     * @param DTOInterface $conditions
     * 
     * @return mixed
     */
    public function rawQuery(string $rawQuery, DTOInterface $conditions): mixed
    {
    }
    /**
     * Summary of readWithOptions
     * @return array
     */
    public function readWithOptions(
        array $selectors = [],
        array $conditions = [],
        array $parameters = [],
        array $optional = []
    ): array {
        return [];
    }
    /**
     * Summary of deleteByIds
     * @param array $ids
     * @return bool
     */
    public function deleteByIds(array $ids): bool{
        return false;
    }
}
