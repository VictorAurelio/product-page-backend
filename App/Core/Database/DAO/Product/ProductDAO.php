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

use App\Core\Database\DAO\DAO;
use App\Core\Database\DAO\DAOInterface;
use App\DTO\DTOInterface;
use App\DTO\Product\ProductDTO;
use App\Models\Product\Product;
use InvalidArgumentException;
use Throwable;

/**
 * ProductDAO is a class that extends the DAO class and implements the DAOInterface
 * It provides basic data access methods such as create, read, update, and delete,
 * as well as methods for retrieving the last inserted ID and reading data
 * with specific options.
 */
class ProductDAO extends DAO implements DAOInterface
{
    /**
     * It is likely used for helping accessing and manipulating dao class.
     *
     * @var DAO
     */
    protected DAO $dao;
    /**
     * Constructs a ProductDAO object with a DAO object and initializes the
     * superclass DAO object with the parameters needed to communicate with
     * the database.
     * 
     * @param Product $productModel
     */
    public function __construct(Product $productModel)
    {
        $this->dao = $productModel->getDao();
        parent::__construct(
            $this->dao->getDataMapper(),
            $this->dao->getQueryBuilder(),
            $this->dao->getSchema(),
            $this->dao->getSchemaID(),
            $this->dao->options
        );
    }
    /**
     * The lastId method returns the last inserted ID from the database by calling
     * the lastId() method of its parent class.
     * 
     * @return int
     */
    public function lastId(): int
    {
        return parent::lastID();
    }
    /**
     * This method receives a DTO with the data to be created and
     * returns true if the creation was successful or false otherwise.
     * 
     * @param DTOInterface $data
     * 
     * @throws InvalidArgumentException
     * 
     * @return ?int
     */
    public function create(DTOInterface $data): ?int
    {
        if (!$data instanceof ProductDTO) {
            throw new InvalidArgumentException(
                'Expected ProductDTO instance.'
            );
        }
        try {
            // Convert ProductDTO to array
            $fields = $data->toArray();
            // echo'<br><br>';var_dump($fields);echo'<br><br>';
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
            if (
                $this->dao
                ->getDataMapper()
                ->numRows() == 1
            ) {
                // Get the last inserted ID and return it
                return $this->dao->lastID();
            }
        } catch (Throwable $throwable) {
            throw $throwable;
        }
        return 0; // Return 0 if the insert fails
    }

    /**
     * The readWithOptions method returns an array of products with selected
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
            'products.id',
            'products.category_id',
            'products.product_name',
            'products.sku',
            'products.price',
            'product_options.option_value',
            'options.option_name',
        ]);
        $query = $this->dao
            ->getQueryBuilder()
            ->buildQuery([
                'type' => 'select',
                'table' => 'products',
                'selectors' => $selectors,
                'conditions' => $conditions,
                'params' => $parameters
            ])
            ->innerJoin(
                'product_options',
                'products.id = product_options.product_id'
            )
            ->innerJoin(
                'options',
                'product_options.option_id = options.id'
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
     * The read method retrieves data from the database based on the
     * given parameters, such as selectors, conditions, and parameters.
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
                // ->innerJoin('product_options', 'products.id = product_options.product_id')
                // ->innerJoin('options', 'product_options.option_id = options.id')
                ->selectQuery();
            $this->dao->getDataMapper()
                ->persist(
                    $query,
                    $this->dao
                        ->getDataMapper()
                        ->buildQueryParameters($conditions, $parameters)
                );
            // var_dump($query);
            if ($this->dao->getDataMapper()->numRows() > 0) {
                return $this->dao->getDataMapper()->results();
            }
        } catch (Throwable $e) {
            return [$e->getMessage()];
        }
        return ['no data'];
    }
    /**
     * This method receives a DTO with the data to be updated and the primary key
     * of the record to be updated. It returns true if the update was successful
     * or false otherwise.
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
        if (!$data instanceof ProductDTO) {
            throw new InvalidArgumentException(
                'Expected ProductDTO instance.'
            );
        }

        $fields = $data->toArray();

        $fieldsWithKeys = [
            'sku' => $fields['_sku'],
            'title' => $fields['_name'],
            'price' => $fields['_price'],
            'category_id' => $fields['_categoryId'],
        ];

        try {
            $args = [
                'table' => $this->dao->getSchema(),
                'type' => 'update',
                'fields' => $fieldsWithKeys,
                'primary_key' => $primaryKey
            ];
            $query = $this->dao
                ->getQueryBuilder()
                ->buildQuery($args)
                ->updateQuery();
            $this->dao->getDataMapper()->persist(
                $query,
                $this->dao
                    ->getDataMapper()
                    ->buildUpdateQueryParameters($fieldsWithKeys)
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
     * This is a method for deleting records from the database. It receives
     * an array of conditions that specifies which records to delete. It then
     * builds and executes a SQL query to delete those records. The method
     * returns true if the number of deleted rows matches the number of
     * conditions and false otherwise.
     * 
     * @param array $conditions
     * 
     * @return bool
     */
    public function delete(array $conditions): bool
    {
        try {
            $args = [
                'table' => $this->dao->getSchema(),
                'type' => 'delete',
                'conditions' => $conditions
            ];
            $query = $this->dao
                ->getQueryBuilder()
                ->buildQuery($args)
                ->deleteQuery();
            $parameters = $this->dao
                ->getDataMapper()
                ->buildDeleteQueryParameters($conditions);
            $this->dao
                ->getDataMapper()
                ->persist($query, $parameters, false, true);

            if (
                $this->dao
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
     * This method receives an array of IDs and creates a set of conditions
     * to delete the corresponding records in the database by calling the delete()
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
                'field' => $this->dao->getSchemaID(),
                'operator' => '=',
                'value' => $id
            ];
        }
        return $this->delete($conditions);
    }
    /**
     * This method receives an personalized sql query as a string and a ProductDTO
     * with the conditions and return the results. If there's no results it'll
     * return an array with the message "no data".
     * Usage example:
     * $rawQuery = "SELECT * FROM products WHERE 
     *      category_id = :category_id AND price < :max_price;";
     * $results = $bookDAO->rawQuery($rawQuery, $productDTO)...
     * 
     * @param string       $rawQuery
     * @param DTOInterface $conditions
     * 
     * @throws InvalidArgumentException
     * 
     * @return array
     */
    public function rawQuery(string $rawQuery, DTOInterface $conditions): array
    {
        if (!$conditions instanceof ProductDTO) {
            throw new InvalidArgumentException(
                'Expected ProductDTO instance.'
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
            if (
                $this->dao
                ->getDataMapper()
                ->numRows() > 0
            ) {
                return $this->dao
                    ->getDataMapper()
                    ->results();
            }
        } catch (Throwable $throwable) {
            throw $throwable;
        }
        return ['no data'];
    }
}
