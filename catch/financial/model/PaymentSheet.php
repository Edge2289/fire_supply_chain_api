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
use think\model\relation\HasMany;

/**
 * Class Payment
 * @package catchAdmin\financial\model
 */
class PaymentSheet extends CatchModel
{
    // 这个根据登陆的账号去获取链接
    protected $connection = 'business';

    protected $name = 'payment_sheet';

    protected $pk = 'id';

    protected $fieldToTime = ['payment_time'];

    protected $fieldToString = ['customer_id'];

    /**
     * @return HasMany
     */
    public function manyPaymentSheetSource(): HasMany
    {
        return $this->hasMany(PaymentSheetSource::class, "payment_sheet_id", "id");
    }

    /**
     * @return mixed|\think\Paginator
     * @throws \think\db\exception\DbException
     */
//    public function getList()
//    {
//        return $this->with([
//            "manyPurchaserOrder",
//            "manyPurchaserOrder.hasPurchaseOrder",
//            "manyPurchaserOrder.hasPurchaseOrder.hasSupplierLicense"
//        ])->catchSearch()->order("id desc")
//            ->paginate();
//    }
}