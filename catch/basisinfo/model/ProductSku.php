<?php
/**
 * Created by PhpStorm.
 * author: xiejiaqing
 * Note: Tired as a dog
 * Date: 2022/1/16
 * Time: 20:58
 */

namespace catchAdmin\basisinfo\model;


use catcher\base\CatchModel;

/**
 * Class ProductSku
 * @package catchAdmin\basisinfo\model
 */
class ProductSku extends CatchModel
{
    protected $name = "product_sku";

    protected $pk = 'id';

    protected $field = [
        'id',
        'product_id',
        'product_code',
        'sku_code',
        'item_number',
        'unit_price',
        'tax_rate',
        'n_tax_price',
        'packing_size',
        'packing_specification',
        'valid_start_time',
        'valid_end_time',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function getValidStartTimeAttr($value)
    {
        return date("Y-m-d", $value);
    }

    public function getValidEndTimeAttr($value)
    {
        return date("Y-m-d", $value);
    }
}