<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/1/16
 * Time: 20:56
 */

namespace catchAdmin\basisinfo\model;


use catcher\base\CatchModel;

/**
 * Class ProductDistributionInfo
 * @package catchAdmin\basisinfo\model
 */
class ProductDistributionInfo extends CatchModel
{
    protected $name = 'product_distribution_info';

    public function setClinicalUseDepartmentAttr($value)
    {
        return implode(",", $value);
    }

    public function getClinicalUseDepartmentAttr($value)
    {
        $map = [];
        foreach (explode(",", $value) as $item) {
            $map[] = (int)$item;
        }
        return $map;
    }

    public function getSigningDateAttr($value)
    {
        if (empty($value)) {
            return "";
        }
        return date("Y-m-d", $value);
    }

    public function getEndTimeAttr($value)
    {
        if (empty($value)) {
            return "";
        }
        return date("Y-m-d", $value);
    }
}