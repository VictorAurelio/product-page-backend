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
 * Summary of DvdDAO
 */
class DvdDAO extends ProductDAO
{
    /**
     * Summary of dvdModel
     *
     * @var Dvd
     */
    protected Dvd $dvdModel;
    /**
     * Summary of __construct
     *
     * @param Dvd $dvdModel
     */
    public function __construct(Dvd $dvdModel)
    {
        parent::__construct($dvdModel);
        $this->dvdModel = $dvdModel;
    }
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
            throw new InvalidArgumentException('Expected DvdDTO instance.');
        }
        try {
            // Convert DvdDTO to array
            $fields = $data->toArray();
            echo '<br>DVDDAO CREATE FIELDS<br>';
            var_dump($fields);
            echo '<br><br>';
            // Remove the 'size' field
            unset($fields['size']);
            unset($fields['id']);
            // echo'<br><br>';var_dump($fields);echo'<br><br>';
            $args = [
                'table' => $this->dao->getSchema(),
                'type' => 'insert',
                'fields' => $fields
            ];
            echo '<br>DVDDAO CREATE ARGS<br>';
            var_dump($args);
            echo '<br><br>';
            $query = $this->dao->getQueryBuilder()->buildQuery($args)->insertQuery();
            echo '<br>CREATE DVDDAO QUERY: <br>';
            var_dump($query);
            echo '<br><br>';
            $this->dao->getDataMapper()->persist(
                $query,
                $this->dao->getDataMapper()->buildInsertQueryParameters($fields)
            );
            echo '<br>CREATE DVDDAO QUERY: <br>';
            var_dump($query);
            echo '<br><br>';
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
            throw new InvalidArgumentException('Expected FurnitureDTO instance.');
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
    public function getAllDvds(): array
    {
        $conditions = ['*'];
        $category_id = $this->dvdModel->getCategoryId();
        $parameters = ["category_id = $category_id"];

        return $this->readWithOptions($conditions, $parameters);
    }
}
