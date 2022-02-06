<?php
/**
 * Created by PhpStorm.
 * author: xiejiaqing
 * Note: Tired as a dog
 * Date: 2022/1/30
 * Time: 23:17
 */

namespace catchAdmin\purchase\model;


use catcher\base\CatchModel;

/**
 * Class PurchaseOrderModel
 * @package catchAdmin\purchase\model
 */
class PurchaseOrder extends CatchModel
{
    // 这个根据登陆的账号去获取链接
    protected $connection = 'business';

    protected $name = 'purchase_order';

}