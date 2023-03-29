<?php

/**
 * This file is part of my Product Page project.
 *
 * @category  Model
 * @package   App\Models\ProductOption
 * @author    Victor Aurélio Rodrigues Ribeiro <victoraurelio_198@hotmail.com>
 * @copyright (c) 2023 Victor Aurelio
 * @link      https://github.com/VictorAurelio/product-page
 */

namespace App\Models\ProductOption;

use App\Core\Database\Connection\ConnectionInterface;
use App\Core\Database\DAO\DAO;
use App\Core\Database\DatabaseService\DatabaseService;
use App\Core\Database\QueryBuilder\MysqlQueryBuilder;
use App\Core\Model;
use App\Core\Database\DAO\Product\ProductOptionDAO;
use App\DTO\Product\ProductOptionDTO;

/**
 * represents product options and their related data.
 */
class ProductOption extends Model
{
    /**
     * represents the table name for the product options.
     */
    protected const TABLESCHEMA = 'product_options';
    /**
     * primary key for product_options table
     */
    protected const TABLESCHEMAID = 'id';
    /**
     * represents the ProductOptionDAO object.
     * 
     * @var ProductOptionDAO
     */
    protected ProductOptionDAO $productOptionDAO;

    /**
     * initializes the ProductOption object and sets the ProductOptionDAO property.
     * 
     * @param ConnectionInterface $connection
     */
    public function __construct(ConnectionInterface $connection)
    {
        parent::__construct(self::TABLESCHEMA, self::TABLESCHEMAID, $connection);
        $this->productOptionDAO = new ProductOptionDAO($this);
    }

    /**
     * returns a new instance of the DAO class for the product options table.
     * 
     * @return DAO
     */
    public function getDao(): DAO
    {
        return new DAO(
            new DatabaseService($this->connection),
            new MysqlQueryBuilder($this->connection),
            self::TABLESCHEMA,
            self::TABLESCHEMAID
        );
    }

    /**
     * Creates a new product option record in the database.
     * Most likely it won't be used in this version of the system.
     * 
     * @param ProductOptionDTO $productOptionDAO
     * 
     * @return bool
     */
    public function createOption(ProductOptionDTO $productOptionDAO): bool
    {
        return $this->productOptionDAO->create($productOptionDAO);
    }
    /**
     * updates an existing product option record in the database.
     * 
     * @param ProductOptionDTO $productOptionDAO
     * 
     * @return bool
     */
    public function updateOption(ProductOptionDTO $productOptionDAO): bool
    {
        return $this->productOptionDAO
                    ->update($productOptionDAO, self::TABLESCHEMAID);
    }
    /**
     * updates the value of an existing product option value in the database.
     * 
     * @param ProductOptionDTO $productOptionDTO
     * @param string $value
     * 
     * @return bool
     */
    public function setOptionValue(
        ProductOptionDTO $productOptionDTO,
        string $value
    ): bool {
        $productOptionDTO->setOptionValue($value);
        return $this->updateOption($productOptionDTO);
    }
    /**
     * finds a product option by its ID and product ID in the database
     * and returns a ProductOptionDTO object, or null if not found.
     * 
     * @param int $optionId
     * @param int $productId
     * 
     * @return ProductOptionDTO|null
     */
    public function findByOptionId(
        int $optionId,
        int $productId
    ): ?ProductOptionDTO {
        return $this->productOptionDAO->findByOptionId($optionId, $productId);
    }
}
