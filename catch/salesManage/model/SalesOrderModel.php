<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/3/6
 * Time: 22:54
 */

namespace catchAdmin\salesManage\model;


use catchAdmin\basisinfo\model\CustomerInfo;
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

    protected $fieldToString = [
        'company_id', 'salesman_id', 'supplier_id', 'customer_info_id', 'settlement_type',
        'settlement_status', 'invoice_status', 'sales_type'
    ];

    protected $fieldToTime = ['sales_time'];

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
                "hasSalesOrderDetails", "hasSalesOrderDetails.hasProductData", "hasSalesOrderDetails.hasProductSkuData",
                "hasSupplierLicense", "hasCustomerInfo"
            ]
        )->order("id desc")
            ->paginate();
        foreach ($data as &$datum) {
            $details = [];
            $goodsDetails = [];
            foreach ($datum['hasSalesOrderDetails'] as $hasPurchaseOrderDetail) {
                list($dataMap, $detail) = $this->assemblyDetailsData($hasPurchaseOrderDetail);
                $goodsDetails[] = $dataMap;
                $details[] = $detail;
            }
            $datum['supplier_name'] = $datum["hasSupplierLicense"]["company_name"] ?? "";
            $datum['customer_name'] = $datum["hasCustomerInfo"]["company_name"] ?? "";

            $datum['goods_details'] = $goodsDetails;
            $datum['detail'] = implode(PHP_EOL, $details);
            unset($datum['hasSalesOrderDetails'], $datum["hasSupplierLicense"]);
        }
        return $data;
    }
}