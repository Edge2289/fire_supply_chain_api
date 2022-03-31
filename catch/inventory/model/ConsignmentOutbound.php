<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/3/29
 * Time: 23:07
 */

namespace catchAdmin\inventory\model;


use catchAdmin\basisinfo\model\CustomerInfo;
use catchAdmin\basisinfo\model\Factory;
use catchAdmin\basisinfo\model\ProductBasicInfo;
use catchAdmin\basisinfo\model\ProductSku;
use catchAdmin\basisinfo\model\SupplierLicense;
use catcher\base\CatchModel;
use think\model\relation\HasMany;
use think\model\relation\HasOne;

/**
 * Class ConsignmentOutbound
 * @package catchAdmin\inventory\model
 */
class ConsignmentOutbound extends CatchModel
{
    protected $connection = 'business';

    protected $name = 'consignment_outbound';

    protected $pk = 'id';

    protected $fieldToTime = ['outbound_time'];
    protected $fieldToString = ['salesman_id', 'supplier_id', 'customer_info_id', 'warehouse_id'];

    public function manyDetails(): HasMany
    {
        return $this->hasMany(ConsignmentOutboundDetails::class, 'consignment_outbound_id', 'id');
    }

    public function hasWarehouse(): HasOne
    {
        return $this->hasOne(Warehouse::class, "id", "warehouse_id");
    }

    public function hasSupplier(): HasOne
    {
        return $this->hasOne(SupplierLicense::class, "id", "supplier_id");
    }

    public function hasFactory(): HasOne
    {
        return $this->hasOne(Factory::class, "id", "factory_id");
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
     * 列表
     *
     * @return mixed|\think\Paginator
     * @throws \think\db\exception\DbException
     * @author 1131191695@qq.com
     */
    public function getList()
    {
        $data = $this->catchSearch()
            ->with([
                "hasWarehouse", "hasSupplier", "hasFactory", "hasCustomerInfo",
                'manyDetails', 'manyDetails.hasInventoryBatchData', "manyDetails.hasProductData", "manyDetails.hasProductSkuData",
            ])
            ->paginate();
        foreach ($data as &$datum) {
            $details = "";
            $goodsDetails = [];
            $datum["warehouse_name"] = $datum["hasWarehouse"]["warehouse_name"] ?? '';
            $datum["supplier_name"] = $datum["hasSupplier"]["company_name"] ?? '';
            $datum["factory_name"] = $datum["hasFactory"]["company_name"] ?? '';
            $datum['customer_name'] = $datum["hasCustomerInfo"]["company_name"];

            foreach ($datum['manyDetails'] as $manyDetail) {
                list($dataMap, $detail) = $this->assemblyBatchItem($manyDetail);
                $goodsDetails[] = $dataMap;
                $details .= $detail;
            }
            $datum['goods'] = $goodsDetails;
            $datum['detail'] = $details;
            unset($datum["hasWarehouse"], $datum["hasSupplier"], $datum["hasCustomerInfo"],
                $datum["hasFactory"], $datum["manyDetails"]);
        }
        return $data;
    }
}