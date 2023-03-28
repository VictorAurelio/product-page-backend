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

class ProductOptionDTO implements DTOInterface
{
    private $_id;
    private $_productId;
    private $_optionId;
    private $_optionValue;
    public function getId() {
        return $this->_id;
    }
    public function setId($id) {
        $this->_id = $id;
    }
    public function getProductId() {
        return $this->_productId;
    }
    public function setProductId($id) {
        $this->_productId = $id;
    }
    public function getOptionId() {
        return $this->_optionId;
    }
    public function setOptionId($optionId) {
        $this->_optionId = $optionId;
    }
    public function getOptionValue() {
        return $this->_optionValue;
    }
    public function setOptionValue($optionValue) {
        $this->_optionValue = $optionValue;
    }
    public function toArray(): array
    {
        return [
            'id' => $this->_id,
            'product_id' => $this->_productId,
            'option_id' => $this->_optionId,
            'option_value' => $this->_optionValue,
        ];
    }
}