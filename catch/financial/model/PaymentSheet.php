<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/2/20
 * Time: 20:02
 */

namespace catchAdmin\financial\model;


use catchAdmin\purchase\model\PurchaseOrder;
use catcher\base\CatchModel;

/**
 * Class Payment
 * @package catchAdmin\financial\model
 */
class PaymentSheet extends CatchModel
{
    // 这个根据登陆的账号去获取链接
    protected $connection = 'business';

    protected $name = 'payment_sheet_many_purchase_order';

    protected $pk = 'id';

    public function hasPurchaseOrder()
    {
        return $this->hasOne(PurchaseOrder::class, "id", "purchase_order_id");
    }
}