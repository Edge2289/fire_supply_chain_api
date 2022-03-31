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
use think\Request;

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
        $field = [
            'product_code' => 's.product_code',
            'sku_code' => 's.sku_code',
            'batch_number' => 'f_inventory_batch.batch_number',
            'serial_number' => 'f_inventory_batch.serial_number',
            'warehouse_id' => 'i.warehouse_id',
        ];
        $params = Request()->param();
        $data = $this->join("f_inventory i", "i.id = f_inventory_batch.inventory_id")
            ->join("f_product_sku s", "s.id = f_inventory_batch.product_sku_id")
            ->field(["f_inventory_batch.*"])
            ->with([
                "hasProduct", "hasProductSku"
            ])
            ->when(true, function ($query) use ($params, $field) {
                if (isset($params['clear'])) {
                    $query->whereRaw("(f_inventory_batch.number - f_inventory_batch.use_number) > 0");
                }
                foreach ($field as $k => $v) {
                    if (isset($params[$k]) && !empty($params[$k])) {
                        $query->where($v, $params[$k]);
                    }
                }
            })
            ->paginate();
        foreach ($data as &$datum) {
            $datum['out_number'] = 0;
            if (isset($params['clear'])) {
                $datum['number'] = ($datum['number'] - $datum['use_number']);
            }
            $datum["product_name"] = $datum["hasProduct"]["product_name"] ?? '';
            $datum["product_sku_name"] = $datum["hasProductSku"]["sku_code"] ?? '';
            unset($datum["hasProduct"], $datum["hasProductSku"]);
        }
        return $data;
    }
}