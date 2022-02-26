<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/1/30
 * Time: 23:17
 */

namespace catchAdmin\purchase\model;


use catchAdmin\basisinfo\model\SuppleInfo;
use catchAdmin\basisinfo\model\SupplierLicense;
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
        return $value ? date("Y-m-d", $value) : $value;
    }

    public function hasPurchaseOrderDetails()
    {
        return $this->hasMany(PurchaseOrderDetails::class, "purchase_order_id", "id");
    }

    /**
     * 关联供货者
     *
     * @return \think\model\relation\HasOne
     * @author 1131191695@qq.com
     */
    public function hasSupplierLicense()
    {
        return $this->hasOne(SupplierLicense::class, "id", "supplier_id");
    }

    /**
     * 获取符合条件的供应商
     *
     * @return mixed
     * @author 1131191695@qq.com
     */
    public function getSupplierLicense()
    {
        return app(SupplierLicense::class)->getSupplier();
    }

    public function getList()
    {
        $data = $this->catchSearch()->with(
            [
                "hasPurchaseOrderDetails", "hasPurchaseOrderDetails.hasProductData", "hasPurchaseOrderDetails.hasProductSkuData", "hasSupplierLicense"
            ]
        )->order("id desc")
            ->paginate();
        foreach ($data as &$datum) {
            $detail = "";
            $goodsDetails = [];
            foreach ($datum['hasPurchaseOrderDetails'] as $hasPurchaseOrderDetail) {
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

            $datum['goods_details'] = $goodsDetails;
            $datum['detail'] = $detail;
            unset($datum['hasPurchaseOrderDetails'], $datum["hasSupplierLicense"]);
        }
        return $data;
    }
}