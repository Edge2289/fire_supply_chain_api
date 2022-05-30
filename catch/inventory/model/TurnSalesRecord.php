<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/4/4
 * Time: 13:18
 */

namespace catchAdmin\inventory\model;


use catchAdmin\basisinfo\model\ProductBasicInfo;
use catchAdmin\basisinfo\model\ProductSku;
use catcher\base\CatchModel;
use think\model\relation\HasOne;

/**
 * Class TurnSalesRecord
 * @package catchAdmin\inventory\model
 */
class TurnSalesRecord extends CatchModel
{
    protected $connection = 'business';

    protected $name = 'turn_sales_record';

    protected $pk = 'id';

    public function hasProductData(): HasOne
    {
        return $this->hasOne(ProductBasicInfo::class, "id", "product_id");
    }

    public function hasProductSkuData(): HasOne
    {
        return $this->hasOne(ProductSku::class, "id", "product_sku_id");
    }

    public function hasInventoryBatch(): HasOne
    {
        return $this->hasOne(InventoryBatch::class, "id", "inventory_batch_id");
    }

}