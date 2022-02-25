<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/2/20
 * Time: 20:02
 */

namespace catchAdmin\financial\model;


use catcher\base\CatchModel;

/**
 * Class Payment
 * @package catchAdmin\financial\model
 */
class Payment extends CatchModel
{
    // 这个根据登陆的账号去获取链接
    protected $connection = 'business';

    protected $name = 'payment_sheet';

    protected $pk = 'id';

    public function getPaymentTimeAttr($value)
    {
        return $this->toDate($value);
    }

    /**
     * 关联采购订单表
     *
     * @return \think\model\relation\HasMany
     * @author 1131191695@qq.com
     */
    public function manyPurchaserOrder()
    {
        return $this->hasMany(PaymentSheet::class, "payment_sheet_id", "id");
    }

    public function getList()
    {
        return $this->with([
            "manyPurchaserOrder",
            "manyPurchaserOrder.hasPurchaseOrder",
            "manyPurchaserOrder.hasPurchaseOrder.hasSupplierLicense"
        ])->catchSearch()->order("id desc")
            ->paginate();
    }
}