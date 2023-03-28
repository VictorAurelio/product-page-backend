<?php

/**
 * This file is part of my Product Page project.
 *
 * @category  DTO
 * @package   App\DTO\Product
 * @author    Victor Aurélio Rodrigues Ribeiro <victoraurelio_198@hotmail.com>
 * @copyright (c) 2023 Victor Aurelio
 * @link      https://github.com/VictorAurelio/product-page
 */

namespace App\DTO\Product;

use App\DTO\DTOInterface;

/**
 * Summary of ProductDTO
 */
abstract class ProductDTO implements DTOInterface
{
    private $_id;
    private $_sku;
    private $_name;
    private $_price;
    private $_categoryId;

    public function getId()
    {
        return $this->_id;
    }
    public function setId($id)
    {
        $this->_id = $id;
    }
    public function getSku()
    {
        return $this->_sku;
    }    
    public function setSku($sku)
    {
        $this->_sku = $sku;
    }    
    public function getName()
    {
        return $this->_name;
    }    
    public function setName($name)
    {
        $this->_name = $name;
    }    
    public function getPrice()
    {
        return $this->_price;
    }    
    public function setPrice($price)
    {
        $this->_price = $price;
    }    
    public function getCategoryId()
    {
        return $this->_categoryId;
    }    
    public function setCategoryId($categoryId)
    {
        $this->_categoryId = $categoryId;
    }
    public function toArray(): array
    {
        return [
            'id' => $this->_id,
            'sku' => $this->_sku,
            'product_name' => $this->_name,
            'price' => $this->_price,
            'category_id' => $this->_categoryId,
        ];
    }
}