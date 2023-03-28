<?php

/**
 * This file is part of my Product Page project.
 *
 * @category  Interface
 * @package   App\Core\Database\QueryBuilder
 * @author    Victor Aurélio Rodrigues Ribeiro <victoraurelio_198@hotmail.com>
 * @copyright (c) 2023 Victor Aurelio
 * @link      https://github.com/VictorAurelio/product-page
 */

namespace App\Core\Database\QueryBuilder;

interface QueryBuilderInterface
{
    public function insertQuery(): string;
    public function selectQuery(): string;
    public function updateQuery(): string;
    public function deleteQuery(): string;
    public function searchQuery(): string;
    public function rawQuery(): string;
}
