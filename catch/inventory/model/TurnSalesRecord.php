<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/4/4
 * Time: 13:18
 */

namespace catchAdmin\inventory\model;


use catcher\base\CatchModel;

/**
 * Class TurnSalesRecord
 * @package catchAdmin\inventory\model
 */
class TurnSalesRecord extends CatchModel
{
    protected $connection = 'business';

    protected $name = 'turn_sales_record';

    protected $pk = 'id';

}