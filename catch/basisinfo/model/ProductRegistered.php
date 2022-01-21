<?php
/**
 * Created by PhpStorm.
 * author: xiejiaqing
 * Note: Tired as a dog
 * Date: 2022/1/16
 * Time: 20:57
 */

namespace catchAdmin\basisinfo\model;


use catcher\base\CatchModel;

/**
 * Class ProductRegistered
 * @package catchAdmin\basisinfo\model
 */
class ProductRegistered extends CatchModel
{
    protected $name = 'product_registered';

    public function getRegisteredTimeAttr($value)
    {
        return date("Y-m-d", $value);
    }

    public function getEndTimeAttr($value)
    {
        return date("Y-m-d", $value);
    }
}