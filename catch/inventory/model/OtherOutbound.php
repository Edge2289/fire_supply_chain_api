<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/3/29
 * Time: 23:07
 */

namespace catchAdmin\inventory\model;


use catcher\base\CatchModel;
use think\model\relation\HasMany;
use think\model\relation\HasOne;

/**
 * Class OtherOutbound
 * @package catchAdmin\inventory\model
 */
class OtherOutbound extends CatchModel
{
    protected $connection = 'business';

    protected $name = 'other_outbound';

    protected $pk = 'id';

    protected $fieldToTime = ['outbound_time'];

    protected $fieldToString = ['salesman_id', 'warehouse_id'];


    public function manyDetails(): HasMany
    {
        return $this->hasMany(OtherOutboundDetails::class, 'other_outbound_id', 'id');
    }

    public function hasWarehouse(): HasOne
    {
        return $this->hasOne(Warehouse::class, "id", "warehouse_id");
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
                "hasWarehouse", 'manyDetails', 'manyDetails.hasInventoryBatchData', "manyDetails.hasProductData", "manyDetails.hasProductSkuData",
            ])
            ->paginate();
        foreach ($data as &$datum) {
            $details = [];
            $goodsDetails = [];
            $datum["warehouse_name"] = $datum["hasWarehouse"]["warehouse_name"] ?? '';

            foreach ($datum['manyDetails'] as $manyDetail) {
                list($dataMap, $detail) = $this->assemblyBatchItem($manyDetail, ['inventory_quantity']);
                $goodsDetails[] = $dataMap;
                $details[] = $detail;
            }
            $datum['goods'] = $goodsDetails;
            $datum['detail'] = implode(PHP_EOL, $details);
            unset($datum["hasWarehouse"], $datum["manyDetails"]);
        }
        return $data;
    }
}