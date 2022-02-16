<?php
/**
 * Created by PhpStorm.
 * author: xiejiaqing
 * Note: Tired as a dog
 * Date: 2022/2/13
 * Time: 20:44
 */

namespace catchAdmin\basisinfo\model;


use catcher\base\CatchModel;

/**
 * Class CustomerRegistration
 * @package catchAdmin\basisinfo\model
 */
class CustomerRegistration extends CatchModel
{
    protected $name = "customer_registration_license";

    protected $pk = 'id';

    public function getRegistrationDateAttr($value)
    {
        return date("Y-m-d", $value);
    }
}