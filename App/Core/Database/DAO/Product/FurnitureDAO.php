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

use App\DTO\Product\FurnitureDTO;
use App\Models\Product\Furniture;
use InvalidArgumentException;
use App\DTO\DTOInterface;
use Throwable;

/**
 * The FurnitureDAO class is a data access object that extends the ProductDAO class.
 * It provides methods for creating, updating, and retrieving furniture from a
 * database.
 */
class FurnitureDAO extends ProductDAO
{
    /**
     * It is likely used for accessing and manipulating furniture data in a database.
     *
     * @var Furniture
     */
    protected Furniture $furnitureModel;
    /**
     * The construct method is the constructor of the FurnitureDAO class.
     * It accepts a furniture object as a parameter, calls the parent constructor with
     * this object, and assigns the FurnitureModel property to this object. This likely
     * sets up the FurnitureDAO object for interacting with furniture data in a database.
     *
     * @param Furniture $furnitureModel
     */
    public function __construct(Furniture $furnitureModel)
    {
        parent::__construct($furnitureModel);
        $this->furnitureModel = $furnitureModel;
    }
    /**
     * The lastId method returns the last inserted ID from the database by calling
     * the lastId() method of its parent class.
     * 
     * @return int
     */
    public function lastId(): int
    {
        return parent::lastId();
    }
    /**
     * This method receives a FurnitureDTO with the data necessary to create a new furniture
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
        if (!$data instanceof FurnitureDTO) {
            throw new InvalidArgumentException(
                'Expected FurnitureDTO instance.'
            );
        }
        try {
            // Convert FurnitureDTO to array
            $fields = $data->toArray();
            // Remove the 'dimensions' field
            unset($fields['dimensions']);

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
                // Get the last inserted ID and return it
                return $this->dao->lastID();
            }
        } catch (Throwable $throwable) {
            throw $throwable;
        }
        return 0; // Return 0 if the insert fails
    }
    /**
     * This method receives a FurnitureDTO with the data necessary to update a furniture
     * and an ID representing the furniture to be updated. It updates the furniture record
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
        if (!$data instanceof FurnitureDTO) {
            throw new InvalidArgumentException('Expected FurnitureDTO instance.');
        }

        // Convert FurnitureDTO to array
        $fields = $data->toArray();

        // Remove the 'dimensions' field
        unset($fields['dimensions']);

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
                        ->buildUpdateQueryParameters($fields)
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
     * The method returns an array of all furnitures that belong to the category ID
     * stored in the furnitureModel object, by calling the readWithOptions()
     * method with specific options.
     * 
     * @return array
     */
    public function getAllFurnitures(): array
    {
        $conditions = ['*'];
        $category_id = $this->furnitureModel->getCategoryId();
        $parameters = ["category_id = $category_id"];

        return $this->readWithOptions($conditions, $parameters);
    }
}
