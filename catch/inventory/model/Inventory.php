<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/2/25
 * Time: 17:17
 */

namespace catchAdmin\inventory\model;


use catchAdmin\basisinfo\model\Factory;
use catchAdmin\basisinfo\model\ProductBasicInfo;
use catchAdmin\basisinfo\model\ProductSku;
use catchAdmin\basisinfo\model\SupplierLicense;
use catcher\base\CatchModel;

/**
 * Class Inventory
 * @package catchAdmin\inventory\model
 */
class Inventory extends CatchModel
{
    protected $connection = 'business';

    protected $name = 'inventory';

    protected $pk = 'id';

    public function hasProduct()
    {
        return $this->hasOne(ProductBasicInfo::class, "id", "product_id");
    }

    public function hasProductSku()
    {
        return $this->hasOne(ProductSku::class, "id", "product_sku_id");
    }

    public function hasWarehouse()
    {
        return $this->hasOne(Warehouse::class, "id", "warehouse_id");
    }

    public function hasSupplier()
    {
        return $this->hasOne(SupplierLicense::class, "id", "supplier_id");
    }

    public function hasFactory()
    {
        return $this->hasOne(Factory::class, "id", "factory_id");
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
                "hasProduct", "hasProductSku", "hasWarehouse", "hasSupplier", "hasFactory"
            ])
            ->paginate();
        foreach ($data as &$datum) {
            $datum["product_name"] = $datum["hasProduct"]["product_name"] ?? '';
            $datum["product_sku_name"] = $datum["hasProductSku"]["sku_code"] ?? '';
            $datum["warehouse_name"] = $datum["hasWarehouse"]["warehouse_name"] ?? '';
            $datum["supplier_name"] = $datum["hasSupplier"]["company_name"] ?? '';
            $datum["factory_name"] = $datum["hasFactory"]["company_name"] ?? '';
            unset($datum["hasProduct"], $datum["hasProductSku"], $datum["hasWarehouse"], $datum["hasSupplier"], $datum["hasFactory"]);
        }
        return $data;
    }
}