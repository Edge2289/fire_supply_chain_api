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
            $detail = "";
            $goodsDetails = [];
            foreach ($datum['hasSalesOrderDetails'] as $hasPurchaseOrderDetail) {
                $goodsDetails[] = [
                    'id' => $hasPurchaseOrderDetail['hasProductSkuData']['id'],
                    'product_id' => $hasPurchaseOrderDetail['hasProductSkuData']['product_id'],
                    'product_code' => $hasPurchaseOrderDetail['hasProductSkuData']['product_code'],
                    'sku_code' => $hasPurchaseOrderDetail['hasProductSkuData']['sku_code'],
                    'item_number' => $hasPurchaseOrderDetail['hasProductSkuData']['item_number'],
                    'unit_price' => $hasPurchaseOrderDetail['hasProductSkuData']['unit_price'],
                    'tax_rate' => $hasPurchaseOrderDetail['hasProductSkuData']['tax_rate'],
                    'n_tax_price' => $hasPurchaseOrderDetail['hasProductSkuData']['n_tax_price'],
                    'packing_size' => $hasPurchaseOrderDetail['hasProductSkuData']['packing_size'],
                    'packing_specification' => $hasPurchaseOrderDetail['hasProductSkuData']['packing_specification'],
                    'product_name' => $hasPurchaseOrderDetail['hasProductData']['product_name'],
                    'udi' => $hasPurchaseOrderDetail['hasProductSkuData']['udi'],
                    'entity' => $hasPurchaseOrderDetail['hasProductSkuData']['entity'],
                    "quantity" => $hasPurchaseOrderDetail["quantity"],
                    "note" => $hasPurchaseOrderDetail["note"],
                ];
                $detail .= sprintf("%s: %s\n", $hasPurchaseOrderDetail['hasProductData']['product_name'], $hasPurchaseOrderDetail["quantity"]);
            }
            $datum['supplier_name'] = $datum["hasSupplierLicense"]["company_name"];
            $datum['customer_name'] = $datum["hasCustomerInfo"]["company_name"];

            $datum['goods_details'] = $goodsDetails;
            $datum['detail'] = $detail;
            unset($datum['hasSalesOrderDetails'], $datum["hasSupplierLicense"]);
        }
        return $data;
    }
}