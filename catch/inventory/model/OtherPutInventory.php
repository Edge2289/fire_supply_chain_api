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

/**
 * Class OtherPutInventory
 * @package catchAdmin\inventory\model
 */
class OtherPutInventory extends CatchModel
{
    protected $connection = 'business';

    protected $name = 'other_put_inventory';

    protected $pk = 'id';

    protected $fieldToTime = ['outbound_time'];

    protected $fieldToString = ['salesman_id', 'customer_info_id', 'warehouse_id'];

    public function hasDetails(): HasMany
    {
        return $this->hasMany(OtherPutInventoryDetails::class, 'other_put_inventory_id', 'id');
    }
}