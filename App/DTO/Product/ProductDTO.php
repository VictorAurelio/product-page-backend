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
 * The ProductDTO class is an abstract class that implements the DTOInterface.
 * It provides getters and setters for the following properties.
 * 
 * This class serves as a base class for other DTO classes that represent different
 * types of products (BookDTO, DvdDTO, and FurnitureDTO). By extending this class,
 * those DTOs inherit the properties and methods of ProductDTO, and they can add
 * their own properties and methods if necessary.
 * 
 */
abstract class ProductDTO implements DTOInterface
{
    /**
     * the product ID
     * 
     * @var
     */
    private $_id;
    /**
     * the product SKU
     * 
     * @var
     */
    private $_sku;
    /**
     * the product name
     * 
     * @var
     */
    private $_name;
    /**
     * the product price
     * 
     * @var
     */
    private $_price;
    /**
     * the product category ID
     * 
     * @var
     */
    private $_categoryId;

    /**
     * retrieve the product ID
     * 
     * @return mixed
     */
    public function getId()
    {
        return $this->_id;
    }
    /**
     * theoretically would modify the product's id
     * 
     * @param mixed $id
     * 
     * @return void
     */
    public function setId($id)
    {
        $this->_id = $id;
    }
    /**
     * retrieve the product SKU
     * 
     * @return mixed
     */
    public function getSku()
    {
        return $this->_sku;
    }    
    /**
     * modify the product's sku
     * 
     * @param mixed $sku
     * 
     * @return void
     */
    public function setSku($sku)
    {
        $this->_sku = $sku;
    }    
    /**
     * retrieve the product name
     * 
     * @return mixed
     */
    public function getName()
    {
        return $this->_name;
    }    
    /**
     * modify the product's name
     * 
     * @param mixed $name
     * 
     * @return void
     */
    public function setName($name)
    {
        $this->_name = $name;
    }    
    /**
     * retrieve the product price
     * 
     * @return mixed
     */
    public function getPrice()
    {
        return $this->_price;
    }    
    /**
     * modify the product's size
     * 
     * @param mixed $price
     * 
     * @return void
     */
    public function setPrice($price)
    {
        $this->_price = $price;
    }    
    /**
     * retrieve the product's category id
     * 
     * @return mixed
     */
    public function getCategoryId()
    {
        return $this->_categoryId;
    }    
    /**
     * modify the product's category id
     * 
     * @param mixed $categoryId
     * 
     * @return void
     */
    public function setCategoryId($categoryId)
    {
        $this->_categoryId = $categoryId;
    }
    /**
     * returns an array with the DTO's properties.
     * 
     * @return array
     */
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