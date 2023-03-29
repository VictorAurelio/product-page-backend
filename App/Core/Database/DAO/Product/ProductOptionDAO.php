<?php

/**
 * This file is part of my Product Page project.
 *
 * @category  DAO
 * @package   App\Core\Database\DAO\Product
 * @author    Victor Aurélio Rodrigues Ribeiro <victoraurelio_198@hotmail.com>
 * @copyright (c) 2023 Victor Aurelio
 * @link      https://github.com/VictorAurelio/product-page
 */

namespace App\Core\Database\DAO\Product;

use App\Models\ProductOption\ProductOption;
use App\Core\Database\DAO\DAOInterface;
use App\Core\Database\DAO\DAO;
use App\DTO\Product\ProductOptionDTO;
use App\DTO\DTOInterface;
use InvalidArgumentException;
use Throwable;

/**
 * This class provides data access methods for ProductOptionDTO objects.
 */
class ProductOptionDAO implements DAOInterface
{
    private DAO $_dao;

    /**
     * Summary of __construct
     * 
     * @param ProductOption $productOptionModel
     */
    public function __construct(ProductOption $productOptionModel)
    {
        $this->_dao = $productOptionModel->getDao();
    }
    /**
     * Creates a new record in the database with the data provided by
     * a ProductOptionDTO object.
     * 
     * @param DTOInterface $data
     * 
     * @throws \InvalidArgumentException
     * 
     * @return int|null
     */
    public function create(DTOInterface $data): ?int
    {
        if (!$data instanceof ProductOptionDTO) {
            throw new \InvalidArgumentException(
                'Expected ProductOptionDTO instance.'
            );
        }
        try {
            // Convert ProductOptionDTO to array
            $fields = $data->toArray();

            $args = [
                'table' => $this->_dao->getSchema(),
                'type' => 'insert',
                'fields' => $fields
            ];
            $query = $this->_dao
                ->getQueryBuilder()
                ->buildQuery($args)
                ->insertQuery();
            $this->_dao
                ->getDataMapper()
                ->persist(
                    $query,
                    $this->_dao
                        ->getDataMapper()
                        ->buildInsertQueryParameters($fields)
                );
            if ($this->_dao->getDataMapper()->numRows() == 1) {
                return $this->_dao->lastID();
            }
        } catch (Throwable $throwable) {
            throw $throwable;
        }

        return 0;
    }
    /**
     * Reads data from the database based on the provided selectors,
     * conditions and parameters.
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
    ): array {
        return [];
    }
    /**
     * This method deletes data based on given conditions and returns a boolean
     * indicating whether the operation was successful or not.
     * 
     * @param array $conditions
     * 
     * @return bool
     */
    public function delete(array $conditions): bool
    {
        try {
            $args = [
                'table' => $this->_dao->getSchema(),
                'type' => 'delete',
                'conditions' => $conditions
            ];
            $query = $this->_dao
                ->getQueryBuilder()
                ->buildQuery($args)
                ->deleteQuery();
            $parameters = $this->_dao
                ->getDataMapper()
                ->buildDeleteQueryParameters($conditions);
            $this->_dao
                ->getDataMapper()
                ->persist($query, $parameters, false, true);

            if (
                $this->_dao
                ->getDataMapper()
                ->numRows() === count($conditions)
            ) {
                return true;
            }
        } catch (Throwable $throwable) {
            throw $throwable;
        }

        return false;
    }
    /**
     * This function executes a personalized SQL query with the provided conditions
     * and returns the results. It throws an exception if the conditions
     * are not of the expected type.
     * 
     * @param string $rawQuery
     * @param DTOInterface $conditions
     * 
     * @throws InvalidArgumentException
     * 
     * @return mixed
     */
    public function rawQuery(string $rawQuery, DTOInterface $conditions): mixed
    {
        if (!$conditions instanceof ProductOptionDTO) {
            throw new InvalidArgumentException(
                'Expected ProductOptionDTO instance.'
            );
        }

        $conditionArray = $conditions->toArray();

        try {
            $this->_dao
                ->getDataMapper()
                ->persist(
                    $rawQuery,
                    $this->_dao
                        ->getDataMapper()
                        ->buildQueryParameters($conditionArray)
                );
            if (
                $this->_dao
                ->getDataMapper()
                ->numRows() > 0
            ) {
                return $this->_dao
                    ->getDataMapper()
                    ->results();
            }
        } catch (Throwable $throwable) {
            throw $throwable;
        }
        return ['no data'];
    }
    /**
     * Returns an array of product options with selected columns and specified conditions.
     *
     * @param array $selectors
     * @param array $conditions
     * @param array $parameters
     *
     * @return array
     */
    public function readWithOptions(
        array $selectors = [],
        array $conditions = [],
        array $parameters = []
    ): array {
        $selectors = array_merge($selectors, [
            'id',
            'product_id',
            'option_id',
            'option_value'
        ]);

        $query = $this->_dao
            ->getQueryBuilder()
            ->buildQuery([
                'type' => 'select',
                'table' => $this->_dao->getSchema(),
                'selectors' => $selectors,
                'conditions' => $conditions,
                'params' => $parameters
            ])
            ->selectQuery();

        $this->_dao
            ->getDataMapper()
            ->persist(
                $query,
                $this->_dao
                    ->getDataMapper()
                    ->buildQueryParameters($conditions, $parameters)
            );
        return $this->_dao->getDataMapper()->results();
    }
    /**
     * Deletes records from the database based on an array of IDs.
     *
     * @param array $ids An array of IDs to be used as a condition for deletion
     * 
     * @return bool Returns a boolean indicating whether the deletion was successful or not.
     */
    public function deleteByIds(array $ids): bool
    {
        $conditions = [];
        foreach ($ids as $id) {
            $conditions[] = [
                'field' => $this->_dao->getSchemaID(),
                'operator' => '=',
                'value' => $id
            ];
        }
        return $this->delete($conditions);
    }
    /**
     * This function is used to find a product option by its optionID and productID
     * It creates a SQL query based on the search selectors and table schema,
     * and then executes the query using the DAO's data mapper. The result is used
     * to create a new ProductOptionDTO instance that contains the found option's
     * information. If no result is found, it returns null. It's used to help with
     * updating a product option.
     * 
     * @param int $optionId
     * @param int $productId
     * 
     * @return ProductOptionDTO|null
     */
    public function findByOptionId(int $optionId, int $productId): ?ProductOptionDTO
    {
        $sqlQuery = $this->_dao
            ->getQueryBuilder()
            ->buildQuery(
                [
                    'type' => 'search',
                    'selectors' => [
                        'option_id' => $optionId,
                        'product_id' => $productId
                    ],
                    'table' => $this->_dao->getSchema()
                ]
            )->exactSearchQuery();

        $this->_dao
            ->getDataMapper()
            ->persist(
                $sqlQuery,
                $this->_dao
                    ->getDataMapper()
                    ->buildQueryParameters(
                        [],
                        [
                            'option_id' => $optionId,
                            'product_id' => $productId
                        ]
                    ),
                false
            );
        $result = $this->_dao
            ->getDataMapper()
            ->result();

        if ($result === null) {
            return null;
        }

        $productOptionDTO = new ProductOptionDTO();
        $productOptionDTO->setId($result->id);
        $productOptionDTO->setProductId($result->product_id);
        $productOptionDTO->setOptionId($result->option_id);
        $productOptionDTO->setOptionValue($result->option_value);

        return $productOptionDTO;
    }
    /**
     * The function updates a record in the database with the provided data and
     * primary key. It expects a ProductOptionDTO object as input, throws
     * an InvalidArgumentException if the input is not of the expected type, and
     * returns a boolean indicating if the update was successful.
     * 
     * @param DTOInterface $data
     * @param string $primaryKey
     * 
     * @throws InvalidArgumentException
     * 
     * @return bool
     */
    public function update(DTOInterface $data, string $primaryKey): bool
    {
        if (!$data instanceof ProductOptionDTO) {
            throw new InvalidArgumentException(
                'Expected ProductOptionDTO instance.'
            );
        }
        $fields = $data->toArray();

        try {
            $args = [
                'table' => $this->_dao->getSchema(),
                'type' => 'update',
                'fields' => $fields,
                'primary_key' => $primaryKey
            ];

            $query = $this->_dao
                ->getQueryBuilder()
                ->buildQuery($args)
                ->updateQuery();

            $this->_dao
                ->getDataMapper()
                ->persist(
                    $query,
                    $this->_dao
                        ->getDataMapper()
                        ->buildUpdateQueryParameters($fields)
                );

            if (
                $this->_dao
                ->getDataMapper()
                ->numRows() === 1
            ) {
                return true;
            }
        } catch (Throwable $throwable) {
            throw $throwable;
        }
        return false;
    }
}
