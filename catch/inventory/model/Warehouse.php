<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/1/11
 * Time: 22:23
 */

namespace catchAdmin\inventory\model;


use catcher\base\CatchModel;

/**
 * Class Warehouse
 * @package catchAdmin\inventory\model
 */
class Warehouse extends CatchModel
{
    protected $connection = 'business';

    protected $name = 'warehouse';

    protected $pk = 'id';

    protected $field = [
        'id',
        'company_id',
        'warehouse_code',
        'warehouse_name',
        'warehouse_type',
        'address',
        'contact',
        'contact_phone',
        'note',
    ];

    public function getList()
    {
        return $this->catchSearch()
            ->paginate();
    }

    public function getWarehouse()
    {
        return $this->where("company_id", request()->user()->company_id)->select()->toArray();
    }

    /**
     * @return array
     * @author 1131191695@qq.com
     */
    public function tableGetWarehouse()
    {
        $map = [];
        foreach ($this->getWarehouse() as $value) {
            $map[] = [
                "value" => (string)$value['id'],
                "label" => $value['warehouse_name'],
            ];
        }
        return $map;
    }
}