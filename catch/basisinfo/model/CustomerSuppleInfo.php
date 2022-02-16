<?php
/**
 * Created by PhpStorm.
 * author: xiejiaqing
 * Note: Tired as a dog
 * Date: 2022/2/14
 * Time: 22:45
 */

namespace catchAdmin\basisinfo\model;


use catcher\base\CatchModel;

/**
 * Class CustomerSuppleInfo
 * @package catchAdmin\basisinfo\model
 */
class CustomerSuppleInfo extends CatchModel
{
    protected $name = "customer_supple_info";

    protected $pk = 'id';

    public function getLicenseStartDateAttr($value)
    {
        return date("Y-m-d", $value);
    }

    public function getLicenseEndDateAttr($value)
    {
        return date("Y-m-d", $value);
    }
}