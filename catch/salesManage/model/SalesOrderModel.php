<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/3/6
 * Time: 22:54
 */

namespace catchAdmin\salesManage\model;


use catchAdmin\basisinfo\model\CustomerLicense;
use catchAdmin\basisinfo\model\SupplierLicense;
use catcher\base\CatchModel;
use think\model\relation\HasMany;
use think\model\relation\HasOne;

/**
 * Class SalesOrderModel
 * @package catchAdmin\salesManage\model
 */
class SalesOrderModel extends CatchModel
{
    protected $connection = 'business';

    protected $name = 'sales_order';

    protected $pk = 'id';

    public function getSalesOrderTimeAttr($value)
    {
        return $this->toDate($value);
    }

    /**
     * 关联订单详情
     * @return HasMany
     * @author 1131191695@qq.com
     */
    public function hasSalesOrderDetails(): hasMany
    {
        return $this->hasMany(SalesOrderDetailsModel::class, "sales_order_id", "id");
    }

    /**
     * 关联供货者
     *
     * @return HasOne
     * @author 1131191695@qq.com
     */
    public function hasSupplierLicense(): HasOne
    {
        return $this->hasOne(SupplierLicense::class, "id", "supplier_id");
    }

    /**
     * 关联客户
     *
     * @return HasOne
     * @author 1131191695@qq.com
     */
    public function hasCustomerLicense(): HasOne
    {
        return $this->hasOne(CustomerLicense::class, "id", "customer_info_id");
    }

    public function getList()
    {

    }
}