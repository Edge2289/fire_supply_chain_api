<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/3/14
 * Time: 20:48
 */

namespace catchAdmin\salesManage\model;


use catchAdmin\basisinfo\model\CustomerInfo;
use catchAdmin\basisinfo\model\SupplierLicense;
use catcher\base\CatchModel;
use think\model\relation\HasMany;
use think\model\relation\HasOne;

/**
 * Class OutboundOrder
 * @package catchAdmin\salesManage\model
 */
class OutboundOrder extends CatchModel
{
    protected $connection = 'business';

    protected $name = 'outbound_order';

    protected $pk = 'id';

    protected $fieldToTime = ['outbound_time'];

    /**
     * 关联出库订单详情
     * @return HasMany
     * @author 1131191695@qq.com
     */
    public function hasOutboundOrderDetails(): hasMany
    {
        return $this->hasMany(OutboundOrderDetails::class, "outbound_order_id", "id");
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
    public function hasCustomerInfo(): HasOne
    {
        return $this->hasOne(CustomerInfo::class, "id", "customer_info_id");
    }

    /**
     * 获取列表
     *
     * @return mixed|\think\Paginator
     * @throws \think\db\exception\DbException
     * @author 1131191695@qq.com
     */
    public function getList()
    {
        $data = $this->catchSearch()->with(
            [
                "hasOutboundOrderDetails", "hasOutboundOrderDetails.hasProductData", "hasOutboundOrderDetails.hasProductSkuData",
                "hasSupplierLicense", "hasCustomerInfo"
            ]
        )->order("id desc")
            ->paginate();
        foreach ($data as &$datum) {
            $details = [];
            $goodsDetails = [];
            foreach ($datum['hasOutboundOrderDetails'] as $hasPurchaseOrderDetail) {
                list($dataMap, $detail) = $this->assemblyDetailsData($hasPurchaseOrderDetail);
                $goodsDetails[] = $dataMap;
                $details[] = $detail;
            }
            $datum['supplier_name'] = $datum["hasSupplierLicense"]["company_name"] ?? "";
            $datum['customer_name'] = $datum["hasCustomerInfo"]["company_name"] ?? "";

            $datum['goods_details'] = $goodsDetails;
            $datum['detail'] = implode(PHP_EOL, $details);
            unset($datum['hasOutboundOrderDetails'], $datum["hasSupplierLicense"]);
        }
        return $data;
    }
}