<?php
/**
 * Created by PhpStorm.
 * author: xiejiaqing
 * Note: Tired as a dog
 * Date: 2022/2/20
 * Time: 14:54
 */

namespace catchAdmin\purchase\model;


use catcher\base\CatchModel;

/**
 * Class ProcurementWarehousingDetails
 * @package catchAdmin\purchase\model
 */
class ProcurementWarehousingDetails extends CatchModel
{
    // 这个根据登陆的账号去获取链接
    protected $connection = 'business';

    protected $name = 'procurement_warehousing_details';

    protected $pk = 'id';
}