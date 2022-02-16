<?php
/**
 * Created by PhpStorm.
 * author: xiejiaqing
 * Note: Tired as a dog
 * Date: 2022/2/13
 * Time: 20:48
 */

namespace catchAdmin\basisinfo\model;


use catcher\base\CatchModel;

/**
 * Class CustomerOperating
 * @package catchAdmin\basisinfo\model
 */
class CustomerOperating extends CatchModel
{
    protected $name = "customer_operating_license";

    protected $pk = 'id';

    public function getBusinessStartDateAttr($value)
    {
        return date("Y-m-d", $value);
    }

    public function getBusinessEndDateAttr($value)
    {
        return date("Y-m-d", $value);
    }
}