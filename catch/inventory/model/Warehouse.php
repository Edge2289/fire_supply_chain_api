<?php
/**
 * Created by PhpStorm.
 * author: xiejiaqing
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
}