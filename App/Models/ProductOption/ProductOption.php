<?php

namespace App\Models\ProductOption;

use App\Core\Database\Connection\ConnectionInterface;
use App\Core\Database\DAO\DAO;
use App\Core\Database\DatabaseService\DatabaseService;
use App\Core\Database\QueryBuilder\MysqlQueryBuilder;
use App\Core\Model;
use App\Core\Database\DAO\Product\ProductOptionDAO;
use App\DTO\Product\ProductOptionDTO;

class ProductOption extends Model
{
    protected const TABLESCHEMA = 'product_options';
    protected const TABLESCHEMAID = 'id';
    protected ProductOptionDAO $productOptionDAO;

    public function __construct(ConnectionInterface $connection)
    {
        parent::__construct(self::TABLESCHEMA, self::TABLESCHEMAID, $connection);
        $this->productOptionDAO = new ProductOptionDAO($this);
    }

    public function getDao(): DAO
    {
        return new DAO(
            new DatabaseService($this->connection),
            new MysqlQueryBuilder($this->connection),
            self::TABLESCHEMA,
            self::TABLESCHEMAID
        );
    }

    public function createOption(ProductOptionDTO $productOptionDAO): bool
    {
        return $this->productOptionDAO->create($productOptionDAO);
    }
    public function updateOption(ProductOptionDTO $productOptionDAO): bool
    {
        return $this->productOptionDAO->update($productOptionDAO, self::TABLESCHEMAID);
    }
    public function setOptionValue(ProductOptionDTO $productOptionDTO, string $value): bool
    {
        $productOptionDTO->setOptionValue($value);
        return $this->updateOption($productOptionDTO);
    }
    public function findByOptionId(int $optionId, int $productId): ?ProductOptionDTO
    {
        return $this->productOptionDAO->findByOptionId($optionId, $productId);
    }
}
