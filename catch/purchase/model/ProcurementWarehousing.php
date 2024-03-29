<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/2/20
 * Time: 14:54
 */

namespace catchAdmin\purchase\model;


use catcher\base\CatchModel;

/**
 * Class ProcurementWarehousing
 * @package catchAdmin\purchase\model
 */
class ProcurementWarehousing extends CatchModel
{
    // 这个根据登陆的账号去获取链接
    protected $connection = 'business';

    protected $name = 'procurement_warehousing';

    protected $pk = 'id';

    protected $fieldToTime = ['put_date', 'inspection_date'];

    protected $fieldToString = [
        'put_user_id', 'warehouse_id', 'purchase_order_id', 'logistics_info',
        'is_qualified', 'courier_company', 'inspection_user_id', 'supplier_id'
    ];

    /**
     * @return \think\model\relation\HasMany
     * @author 1131191695@qq.com
     */
    public function hasProcurementWarehousingDetails(): \think\model\relation\HasMany
    {
        return $this->hasMany(ProcurementWarehousingDetails::class, "procurement_warehousing_id", "id");
    }

    /**
     * @return \think\model\relation\HasMany
     * @author 1131191695@qq.com
     */
    public function hasPurchaseOrder(): \think\model\relation\hasOne
    {
        return $this->hasOne(PurchaseOrder::class, "id", "purchase_order_id");
    }

    /**
     * @return mixed|\think\Paginator
     * @throws \think\db\exception\DbException
     * @author 1131191695@qq.com
     */
    public function getList()
    {
        return $this->catchSearch()->with([
            "hasProcurementWarehousingDetails"
        ])
            ->order("id desc")
            ->paginate();
    }
}