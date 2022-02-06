<?php
/**
 * Created by PhpStorm.
 * author: xiejiaqing
 * Note: Tired as a dog
 * Date: 2022/1/30
 * Time: 23:46
 */

namespace catchAdmin\purchase\model;


/**
 * Class PurchaseOrderDetails
 * @package catchAdmin\purchase\model
 */
class PurchaseOrderDetails
{
    // 这个根据登陆的账号去获取链接
    protected $connection = 'business';

    protected $name = "purchase_order_details";
}