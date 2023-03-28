<?php

/**
 * This file is part of my Product Page project.
 *
 * @category  Sanitizer
 * @package   App\Core\Validation\Rule\Data
 * @author    Victor Aurélio Rodrigues Ribeiro <victoraurelio_198@hotmail.com>
 * @copyright (c) 2023 Victor Aurelio
 * @link      https://github.com/VictorAurelio/product-page
 */

namespace App\Core\Validation\Rule\Data;

use App\Core\Exceptions\AppInvalidArgumentException;
use App\Core\Validation\Rule\Data\DataSanitizer;

class DataValidator
{
    public function __construct(array $dirtyData)
    {
        if (empty($dirtyData)) {
            throw new AppInvalidArgumentException('No data was submitted.');
        }
        if (is_array($dirtyData)) {
            foreach ($this->cleanData($dirtyData) as $key => $value) {
                $this->$key = $value;
            }
        }
    }
    private function cleanData(array $dirtyData) : array
    {
        $cleanData = DataSanitizer::clean($dirtyData);
        if($cleanData) {
            return $cleanData;
        }
        return ['Invalid data'];
    }
}
