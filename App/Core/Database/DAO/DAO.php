<?php

/**
 * This file is part of my Product Page project.
 *
 * @category  DAO
 * @package   App\Core\Database\DAO
 * @author    Victor Aurélio Rodrigues Ribeiro <victoraurelio_198@hotmail.com>
 * @copyright (c) 2023 Victor Aurelio
 * @link      https://github.com/VictorAurelio/product-page
 */

namespace App\Core\Database\DAO;

use App\Core\Database\DatabaseService\DatabaseService;
use App\Core\Database\QueryBuilder\QueryBuilder;
use Throwable;

/**
 * Summary of DAO
 */
class DAO
{
    /**
     * Summary of queryBuilder
     *
     * @var QueryBuilder
     */
    protected QueryBuilder $queryBuilder;
    /**
     * Summary of dataMapper
     *
     * @var DatabaseService
     */
    protected DatabaseService $dataMapper;
    /**
     * Summary of tableSchemaID
     *
     * @var string
     */
    protected string $tableSchemaID;
    /**
     * Summary of tableSchema
     *
     * @var string
     */
    protected string $tableSchema;
    /**
     * Summary of options
     *
     * @var array
     */
    protected array $options;

    /**
     * Summary of __construct
     * 
     * @param DatabaseService $dataMapper
     * @param QueryBuilder    $queryBuilder 
     * @param string          $tableSchema
     * @param string          $tableSchemaID
     * @param array|null      $options
     */
    public function __construct(
        DatabaseService $dataMapper,
        QueryBuilder $queryBuilder,
        string $tableSchema,
        string $tableSchemaID,
        ?array $options = []
    ) {
        $this->tableSchemaID = $tableSchemaID;
        $this->queryBuilder = $queryBuilder;
        $this->tableSchema = $tableSchema;
        $this->dataMapper = $dataMapper;
        $this->options = $options;
    }

    /**
     * Summary of GetSchema
     * 
     * @inheritdoc
     *
     * @return string
     */
    public function getSchema(): string
    {
        return (string)$this->tableSchema;
    }

    /**
     * Get the QueryBuilder instance.
     *
     * @return QueryBuilder
     */
    public function getQueryBuilder(): QueryBuilder
    {
        return $this->queryBuilder;
    }

    /**
     * Get the DataMapper instance.
     *
     * @return DatabaseService
     */
    public function getDataMapper(): DatabaseService
    {
        return $this->dataMapper;
    }

    /**
     * Summary of GetSchemaId
     *
     * @inheritdoc
     *
     * @return string
     */
    public function getSchemaID(): string
    {
        return (string)$this->tableSchemaID;
    }

    /**
     * Summary of LastId
     * 
     * @inheritdoc
     *
     * @return integer
     */
    public function lastID(): int
    {
        return $this->dataMapper->getLastId();
    }
    

    /**
     * Summary of Where
     *
     * @param array $conditions
     * 
     * @return DAO
     */
    public function where(array $conditions = []): self
    {
        $args = [
            'table' => $this->getSchema(),
            'type' => 'select',
            'selectors' => [],
            'conditions' => $conditions,
            'params' => []
        ];
        $this->queryBuilder->buildQuery($args)->selectQuery();
        return $this;
    }

    /**
     * Summary of FindByExact
     *
     * @param array $fields
     * 
     * @return object|null
     */
    public function findByExact(array $fields): ?Object
    {
        $sqlQuery = $this->queryBuilder->buildQuery(
            [
                'type' => 'search',
                'selectors' => $fields,
                'table' => $this->getSchema()
            ]
        )->exactSearchQuery();

        $this->dataMapper->persist(
            $sqlQuery,
            $this->dataMapper->buildQueryParameters([], $fields),
            false
        );
        return $this->dataMapper->result();
    }

    /**
     * Summary of Search
     *
     * @param array $selectors
     * @param array $conditions
     * @param bool  $exact
     * 
     * @inheritdoc
     * 
     * @return array
     * 
     * @throws DataLayerException
     */
    public function search(
        array $selectors = [],
        array $conditions = [],
        bool $exact = false
    ): array {
        $args = [
            'table' => $this->getSchema(),
            'type' => 'search',
            'selectors' => $selectors,
            'conditions' => $conditions,
            'isSearch' => !$exact
        ];
        $query = $exact ?
            $this->queryBuilder->buildQuery($args)->searchQueryExact() :
            $this->queryBuilder->buildQuery($args)->searchQuery();

        $this->dataMapper->persist(
            $query,
            $this->dataMapper->buildQueryParameters($conditions),
            true
        );
        return ($this->dataMapper->numRows() >= 1)
             ? $this->dataMapper->results() 
             : array();
    }
    /**
     * Summary of GetQueryType
     * 
     * @param string $type
     * 
     * @return mixed
     */
    public function getQueryType(string $type)
    {
        $queryTypes = [
            'createQuery',
            'readQuery',
            'updateQuery',
            'deleteQuery',
            'joinQuery',
            'searchQuery',
            'rawQuery'
        ];
        if (!empty($type)) {
            if (in_array($type, $queryTypes, true)) {
                return $this->$type;
            }
        }
    }
}
