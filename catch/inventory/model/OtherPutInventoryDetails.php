<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/3/29
 * Time: 23:07
 */

namespace catchAdmin\inventory\model;


use catchAdmin\basisinfo\model\ProductBasicInfo;
use catchAdmin\basisinfo\model\ProductSku;

/**
 * Class OtherPutInventory
 * @package catchAdmin\inventory\model
 */
class OtherPutInventoryDetails
{
    protected $connection = 'business';

    protected $name = 'other_put_inventory_details';

    protected $pk = 'id';

    public function hasInventoryBatchData()
    {
        return $this->hasOne(InventoryBatch::class, "id", "inventory_batch_id");
    }

    public function hasProductData()
    {
        return $this->hasOne(ProductBasicInfo::class, "id", "product_id");
    }

    public function hasProductSkuData()
    {
        return $this->hasOne(ProductSku::class, "id", "product_sku_id");
    }
}