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

use InvalidArgumentException;
use App\DTO\Product\BookDTO;
use App\Models\Product\Book;
use App\DTO\DTOInterface;
use Throwable;

/**
 * Summary of BookDAO
 */
class BookDAO extends ProductDAO
{
    /**
     * Summary of bookModel
     *
     * @var Book
     */
    protected Book $bookModel;
    /**
     * Summary of __construct
     *
     * @param Book $bookModel
     */
    public function __construct(Book $bookModel)
    {
        parent::__construct($bookModel);
        $this->bookModel = $bookModel;
    }
    public function lastId(): int
    {
        return parent::lastId();
    }
    /**
     * This method receives a BookDTO with the data necessary to create a new book
     * and creates a new record in the database. It returns true if the creation
     * was successful and false otherwise.
     *
     * @param DTOInterface $data
     *
     * @throws InvalidArgumentException
     *
     * @return ?int
     */
    public function create(DTOInterface $data): ?int
    {
        if (!$data instanceof BookDTO) {
            throw new InvalidArgumentException('Expected BookDTO instance.');
        }
        try {
            // Convert BookDTO to array
            $fields = $data->toArray();
            // Remove the 'weight' field
            unset($fields['weight']);
            
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
                // Get the last inserted ID and return it
                return $this->dao->lastID();
            }
        } catch (Throwable $throwable) {
            throw $throwable;
        }
        return 0; // Return 0 if the insert fails
    }
    /**
     * This method receives a BookDTO with the data necessary to update a book
     * and an ID representing the book to be updated. It updates the book record
     * in the database and returns true if the update was successful and false
     * otherwise.
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
        if (!$data instanceof BookDTO) {
            throw new InvalidArgumentException('Expected FurnitureDTO instance.');
        }

        // Convert BookDTO to array
        $fields = $data->toArray();

        // Remove the 'weight' field
        unset($fields['weight']);

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
                $this->dao->getDataMapper()->buildUpdateQueryParameters($fields)
            );
            if ($this->dao->getDataMapper()->numRows() === 1) {
                return true;
            }
        } catch (Throwable $throwable) {
            throw $throwable;
        }

        return false;
    }
    public function getAllBooks(): array {
        $conditions = ['*'];
        $category_id = $this->bookModel->getCategoryId();
        $parameters = ["category_id = $category_id"];
    
        return $this->readWithOptions($conditions, $parameters);
    }   
}
