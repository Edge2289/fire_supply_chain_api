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

    protected $pk = 'id';

    public function getUserIdAttr($value)
    {
        return (string)$value;
    }

    public function getSupplierIdAttr($value)
    {
        return (string)$value;
    }

    public function getSettlementStatusAttr($value)
    {
        return (string)$value;
    }

    public function getPurchaseDateAttr($value)
    {
        return $value? date("Y-m-d", $value): $value;
    }

    public function hasPurchaseOrderDetails()
    {
        return $this->hasMany(PurchaseOrderDetails::class, "purchase_order_id", "id");
    }

    public function getList()
    {
        $data = $this->catchSearch()->with("hasPurchaseOrderDetails")->order("id desc")
            ->paginate();
        foreach ($data as &$datum) {
            $datum['goods_details'] = $datum['hasPurchaseOrderDetails'];
        }
        return $data;
    }
}