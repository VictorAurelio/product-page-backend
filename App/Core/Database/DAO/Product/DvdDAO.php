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
use App\DTO\Product\DvdDTO;
use App\Models\Product\Dvd;
use App\DTO\DTOInterface;
use Throwable;

/**
 * The DvdDAO class is a data access object that extends the ProductDAO class.
 * It provides methods for creating, updating, and retrieving dvds from a
 * database.
 */
class DvdDAO extends ProductDAO
{
    /**
     * It is likely used for accessing and manipulating dvd data in a database.
     *
     * @var Dvd
     */
    protected Dvd $dvdModel;
    /**
     * The construct method is the constructor of the DvdDAO class.
     * It accepts a Dvd object as a parameter, calls the parent constructor with
     * this object, and assigns the dvdModel property to this object. This likely
     * sets up the dvdDAO object for interacting with dvd data in a database.
     *
     * @param Dvd $dvdModel
     */
    public function __construct(Dvd $dvdModel)
    {
        parent::__construct($dvdModel);
        $this->dvdModel = $dvdModel;
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
     * This method receives a DvdDTO with the data necessary to create a new dvd
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
        if (!$data instanceof DvdDTO) {
            throw new InvalidArgumentException(
                'Expected DvdDTO instance.'
            );
        }
        try {
            // Convert DvdDTO to array
            $fields = $data->toArray();
            // Remove the 'size' field
            unset($fields['size']);
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
     * This method receives a DvdDTO with the data necessary to update a dvd
     * and an ID representing the dvd to be updated. It updates the dvd record
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
        if (!$data instanceof DvdDTO) {
            throw new InvalidArgumentException(
                'Expected FurnitureDTO instance.'
            );
        }

        // Convert DvdDTO to array
        $fields = $data->toArray();

        // Remove the 'size' field
        unset($fields['size']);

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
     * The method returns an array of all dvds that belong to the category ID
     * stored in the dvdModel object, by calling the readWithOptions()
     * method with specific options.
     * 
     * @return array
     */
    public function getAllDvds(): array
    {
        $conditions = ['*'];
        $category_id = $this->dvdModel->getCategoryId();
        $parameters = ["category_id = $category_id"];

        return $this->readWithOptions($conditions, $parameters);
    }
}
