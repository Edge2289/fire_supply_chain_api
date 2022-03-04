<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/2/25
 * Time: 17:17
 */

namespace catchAdmin\inventory\model;


use catchAdmin\basisinfo\model\ProductBasicInfo;
use catchAdmin\basisinfo\model\ProductSku;
use catcher\base\CatchModel;

/**
 * Class InventoryBatch
 * @package catchAdmin\inventory\model
 */
class InventoryBatch extends CatchModel
{
    protected $connection = 'business';

    protected $name = 'inventory_batch';

    protected $pk = 'id';

    public function hasProduct()
    {
        return $this->hasOne(ProductBasicInfo::class, "id", "product_id");
    }

    public function hasProductSku()
    {
        return $this->hasOne(ProductSku::class, "id", "product_sku_id");
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
                "hasProduct", "hasProductSku"
            ])
            ->paginate();
        foreach ($data as &$datum) {
            $datum["product_name"] = $datum["hasProduct"]["product_name"] ?? '';
            $datum["product_sku_name"] = $datum["hasProductSku"]["sku_code"] ?? '';
            unset($datum["hasProduct"], $datum["hasProductSku"]);
        }
        return $data;
    }
}