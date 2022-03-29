<?php
declare(strict_types=1);

namespace catcher\base;

use catcher\CatchQuery;
use catcher\traits\db\BaseOptionsTrait;
use catcher\traits\db\RewriteTrait;
use catcher\traits\db\WithTrait;
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
            return $result;
        };
        $this->maker($changeTime);
        $this->filter($changeTime);
    }

    /**
     * 数据库转换日期
     *
     * @param $value
     * @return false|string
     * @author 1131191695@qq.com
     */
    protected function toDate($value)
    {
        if (empty($value)) {
            return "";
        }
        if (is_string($value)) {
            return $value;
        }
        return date("Y-m-d", $value);
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
     * @param $hasPurchaseOrderDetail
     * @return array
     * @author 1131191695@qq.com
     */
    protected function assemblyDetailsData($hasPurchaseOrderDetail)
    {
        $data = [
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
        $detail = sprintf("商品: %s, 数量:%s\n", $hasPurchaseOrderDetail['hasProductData']['product_name'], $hasPurchaseOrderDetail["quantity"]);
        return [$data, $detail];
    }
}
