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
     * 字段数据转string
     *
     * @param $data
     * @return array
     * @author 1131191695@qq.com
     */
    protected function fieldToFormat($data)
    {
        if (!$this->fieldToString) {
            return $data;
        }
        foreach ($data as &$datum) {
            // to string
            foreach ($this->fieldToString as $field) {
                if (isset($datum[$field])) {
                    $datum[$field] = (string)$datum[$field];
                }
            }
            // to time
            foreach ($this->fieldToTime as $field) {
                if (isset($datum[$field])) {
                    if (is_string($datum[$field])) {
                        continue;
                    }
                    $datum[$field] = date("Y-m-d", (int)$datum[$field]);
                }
            }
        }
        return $data;
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
}
