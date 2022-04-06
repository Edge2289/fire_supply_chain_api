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
 * Class OtherOutbound
 * @package catchAdmin\inventory\model
 */
class OtherOutbound extends CatchModel
{
    protected $connection = 'business';

    protected $name = 'other_outbound';

    protected $pk = 'id';

    protected $fieldToTime = ['outbound_time'];

    protected $fieldToString = ['salesman_id', 'customer_info_id', 'warehouse_id'];

    public function hasDetails(): HasMany
    {
        return $this->hasMany(OtherOutboundDetails::class, 'other_outbound_id', 'id');
    }

}