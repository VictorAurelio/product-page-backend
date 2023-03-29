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
 * Class UserDAO implements DAOInterface, with CRUD methods for UserDTO.
 */
class UserDAO implements DAOInterface
{
    /**
     * DAO object.
     *
     * @var DAO
     */
    protected DAO $dao;

    /**
     * Constructor for UserDAO class that takes a DAO object as a parameter and
     * assigns it to the protected $dao property.
     * 
     * @param DAO $dao
     */
    public function __construct(DAO $dao)
    {
        $this->dao = $dao;
    }
    /**
     * Creates a new record in the database table and returns the last inserted row ID.
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
            throw new InvalidArgumentException(
                'Expected UserDTO instance.'
            );
        }
        try {
            // Convert UserDTO to array
            $fields = $data->toArray();

            $args = [
                'table' => $this->dao->getSchema(),
                'type' => 'insert',
                'fields' => $fields
            ];
            $query = $this->dao
                ->getQueryBuilder()
                ->buildQuery($args)
                ->insertQuery();
            $this->dao
                ->getDataMapper()
                ->persist(
                    $query,
                    $this->dao
                        ->getDataMapper()
                        ->buildInsertQueryParameters($fields)
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
     * Reads data from the database according to the specified parameters,
     * using the SELECT query, and returns the results as an array. The function
     * uses the DAO object's schema, query builder, and data mapper to construct
     * and execute the query
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
            $query = $this->dao
                ->getQueryBuilder()
                ->buildQuery($args)
                ->selectQuery();
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
            return [$e->getMessage()];
        }
        return ['no data'];
    }
    /**
     * Updates a user record with the given data and primary key.
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
            throw new InvalidArgumentException(
                'Expected UserDTO instance.'
            );
        }

        $fields = $data->toArray();

        try {
            $args = [
                'table' => $this->dao->getSchema(),
                'type' => 'update',
                'fields' => $fields,
                'primary_key' => $primaryKey
            ];
            $query = $this->dao
                ->getQueryBuilder()
                ->buildQuery($args)
                ->updateQuery();
            $this->dao
                ->getDataMapper()
                ->persist(
                    $query,
                    $this->dao
                        ->getDataMapper()
                        ->buildQueryParameters($fields)
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
     * The delete function of the UserDAO class takes an array of conditions,
     * builds a delete query using the DAO's query builder, and executes it
     * using the data mapper. If the query affects one row, it returns true.
     * Otherwise, it returns false. If an error occurs, it throws
     * the corresponding exception.
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
            $query = $this->dao
                ->getQueryBuilder()
                ->buildQuery($args)
                ->deleteQuery();
            $this->dao
                ->getDataMapper()
                ->persist(
                    $query,
                    $this->dao
                        ->getDataMapper()
                        ->buildDeleteQueryParameters($conditionArray)
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
     * Finds a user by their email in the database and returns a UserDTO instance
     * with the user's information. Throws an exception if the parameter passed
     * is not an instance of UserDTO.
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
            throw new InvalidArgumentException(
                'Expected UserDTO instance.'
            );
        }

        $fieldsArray = $fields->toArray();

        $sqlQuery = $this->dao
            ->getQueryBuilder()
            ->buildQuery(
                [
                    'type' => 'search',
                    'selectors' => ['email' => $fieldsArray['email']],
                    'table' => $this->dao->getSchema()
                ]
            )->exactSearchQuery();

        $this->dao
            ->getDataMapper()
            ->persist(
                $sqlQuery,
                $this->dao
                    ->getDataMapper()
                    ->buildQueryParameters(
                        [],
                        ['email' => $fieldsArray['email']]
                    ),
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
     * This method receives a personalized SQL query as a string and a UserDTO
     * with the conditions and returns the results. If there are no results,
     * it returns an array with the message "no data". It throws an
     * InvalidArgumentException if the conditions parameter is not
     * an instance of UserDTO.
     * 
     * @param string       $rawQuery
     * @param DTOInterface $conditions
     * 
     * @return mixed
     */
    public function rawQuery(string $rawQuery, DTOInterface $conditions): array
    {
        if (!$conditions instanceof UserDTO) {
            throw new InvalidArgumentException(
                'Expected UserDTO instance.'
            );
        }

        $conditionArray = $conditions->toArray();

        try {
            $this->dao
                ->getDataMapper()
                ->persist(
                    $rawQuery,
                    $this->dao
                        ->getDataMapper()
                        ->buildQueryParameters($conditionArray)
                );

            if ($this->dao->getDataMapper()->numRows() > 0) {
                return $this->dao->getDataMapper()->results();
            }
        } catch (Throwable $throwable) {
            throw $throwable;
        }

        return ['no data'];
    }
    /**
     * The readWithOptions method returns an array of * with selected
     * columns and specified conditions, by performing a join operation on
     * two tables, and executing a select query using the passed options.
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
    ): array {
        $selectors = array_merge($selectors, [
            ''
        ]);
        $query = $this->dao
            ->getQueryBuilder()
            ->buildQuery([
                'type' => 'select',
                'table' => '',
                'selectors' => $selectors,
                'conditions' => $conditions,
                'params' => $parameters
            ])
            ->innerJoin(
                '',
                ''
            )
            ->innerJoin(
                '',
                ''
            )
            ->selectQuery();
        $this->dao
            ->getDataMapper()
            ->persist(
                $query,
                $this->dao
                    ->getDataMapper()
                    ->buildQueryParameters($conditions, $parameters)
            );
        $results = $this->dao
            ->getDataMapper()
            ->results();

        return $results;
    }
    /**
     * This method receives an array of user IDs and creates a set of conditions
     * to delete the corresponding user records in the database by calling the delete()
     * method. It returns a boolean value indicating whether the deletion was
     * successful or not.
     *
     * @param array $ids
     *
     * @return bool
     */
    public function deleteByIds(array $ids): bool
    {
        $conditions = [];
        foreach ($ids as $id) {
            $conditions[] = [
                'field' => 'id',
                'operator' => '=',
                'value' => $id
            ];
        }
        return $this->delete($conditions);
    }
}
