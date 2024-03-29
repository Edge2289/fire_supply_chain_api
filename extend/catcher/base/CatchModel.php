<?php
declare(strict_types=1);

namespace catcher\base;

use catcher\CatchQuery;
use catcher\traits\db\BaseOptionsTrait;
use catcher\traits\db\RewriteTrait;
use catcher\traits\db\WithTrait;
use think\facade\Db;
use think\model\concern\SoftDelete;
use catcher\traits\db\ScopeTrait;

/**
 *
 * @mixin CatchQuery
 * Class CatchModel
 * @package catcher\base
 */
abstract class CatchModel extends \think\Model
{
    use SoftDelete, BaseOptionsTrait, ScopeTrait, RewriteTrait, WithTrait;

    protected $createTime = 'created_at';

    protected $updateTime = 'updated_at';

    protected $deleteTime = 'deleted_at';

    protected $defaultSoftDelete = 0;

    protected $autoWriteTimestamp = true;

    protected $fieldToString = [];

    protected $fieldToTime = [];

    protected $fieldNumberToEmpty = [];

    // 分页 Limit
    public const LIMIT = 10;
    // 开启
    public const ENABLE = 1;
    // 禁用
    public const DISABLE = 2;

    /**
     * 是否有 field
     *
     * @time 2020年11月23日
     * @param string $field
     * @return bool
     */
    public function hasField(string $field)
    {
        return property_exists($this, 'field') && in_array($field, $this->field);
    }

    public function __construct(array $data = [])
    {
        parent::__construct($data);

        if (method_exists($this, 'autoWithRelation')) {
            $this->autoWithRelation();
        }
        $changeTime = function ($result) {
            if ($result->isEmpty()) {
                return $result;
            }
            foreach ($this->fieldToString as $field) {
                if (isset($result->{$field})) {
                    $result->{$field} = (string)$result->{$field};
                }
            }
            foreach ($this->fieldToTime as $value) {
                if (isset($result->{$value}) && !empty($result->{$value}) && !is_string($result->{$value})) {
                    $result->{$value} = date("Y-m-d", (int)$result->{$value});
                }
            }
            foreach ($this->fieldNumberToEmpty as $field) {
                if (isset($result->{$field}) && $result->{$field} == 0) {
                    $result->{$field} = "";
                }
            }
            return $result;
        };
        $this->maker($changeTime);
        $this->filter($changeTime);
    }

    /**
     * 添加排它锁
     *
     * @param $id
     * @return array|CatchModel|mixed|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author 1131191695@qq.com
     */
    public function getFindByKey($id)
    {
        return $this->where("id", $id)->lock(true)->find();
    }

    /**
     * 组装
     *
     * @param $goodsDetailsData
     * @return array
     * @author 1131191695@qq.com
     */
    public function assemblyDetailsData($goodsDetailsData)
    {
        // 单位
        // 含税单价
        $unit_price = [];
        $procurement_price = [];
        for ($i = 1; $i < 5; $i++) {
            $unit_price[] = [
                "label" => $i,
                "value" => $goodsDetailsData['hasProductSkuData']['unit_price_' . $i] ?? 0
            ];
        }
        for ($i = 1; $i < 3; $i++) {
            $procurement_price[] = [
                "label" => $i,
                "value" => $goodsDetailsData['hasProductSkuData']['procurement_price_' . $i]
            ];
        }
        $data = [
            'id' => $goodsDetailsData['hasProductSkuData']['id'],
            'product_id' => $goodsDetailsData['hasProductSkuData']['product_id'],
            'product_code' => $goodsDetailsData['hasProductSkuData']['product_code'],
            'sku_code' => $goodsDetailsData['hasProductSkuData']['sku_code'],
            'item_number' => $goodsDetailsData['hasProductSkuData']['item_number'],
            'unit_price' => $goodsDetailsData['unit_price'],
            'product_name' => $goodsDetailsData['hasProductData']['product_name'],
            'udi' => $goodsDetailsData['hasProductSkuData']['udi'],
            'entity' => $goodsDetailsData['entity'],
            "quantity" => $goodsDetailsData["quantity"],
            "note" => $goodsDetailsData["note"],
            "hasProductEntity" => $goodsDetailsData['hasProductSkuData']->hasProductEntity->toArray(),
            "unit_price_item" => $unit_price,
            "procurement_price_item" => $procurement_price,
        ];
        $detail = sprintf("商品: %s, 数量:%s", $data['product_name'], $data["quantity"]);
        return [$data, $detail];
    }

    /**
     * 组装库存数据
     *
     * @param $data
     * @return array
     * @author 1131191695@qq.com
     */
    protected function assemblyBatchItem($data, $addFormData = [])
    {
        $map = [
            "id" => $data['hasInventoryBatchData']['id'],
            "details_id" => $data['id'],
            "inventory_id" => $data['inventory_id'],
            "inventory_batch_id" => $data['inventory_batch_id'],
            "product_id" => $data['product_id'],
            "product_sku_id" => $data['product_sku_id'],
            "product_code" => $data['product_code'],
            "item_number" => $data['item_number'],
            "sku_code" => $data['hasProductSkuData']['sku_code'],
            "tax_rate" => $data['hasProductSkuData']['tax_rate'],
            "unit_price" => $data['hasProductSkuData']['unit_price'],
            "batch_number" => $data['hasInventoryBatchData']['batch_number'],
            "serial_number" => $data['hasInventoryBatchData']['serial_number'],
            "production_date" => $data['hasInventoryBatchData']['production_date'],
            "valid_until" => $data['hasInventoryBatchData']['valid_until'],
            "registration_number" => $data['hasInventoryBatchData']['registration_number'],
            "number" => ($data['hasInventoryBatchData']['number'] - $data['hasInventoryBatchData']['use_number'] + $data['quantity']),
            "put_num" => $data['quantity'],
            "product_name" => $data['hasProductData']['product_name'],
            "product_sku_name" => $data['hasProductSkuData']['sku_code'],
        ];
        foreach ($addFormData as $key) {
            if (isset($data[$key])) {
                $map[$key] = $data[$key];
            }
        }
        $detail = sprintf("商品: %s, 数量:%s", $data['hasProductData']['product_name'], $data["quantity"]);
        return [$map, $detail];
    }
}
