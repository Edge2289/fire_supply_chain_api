<?php
/**
 * Created by PhpStorm.
 * author: meridian(1131191695@qq.com)
 * Note: Tired as a dog
 * Date: 2022/6/4
 * Time: 23:02
 */

namespace catchAdmin\financial\model;


use catcher\base\CatchModel;

/**
 * Class PaymentSheetSource
 * @package catchAdmin\financial\model
 */
class PaymentSheetSource extends CatchModel
{
    // 这个根据登陆的账号去获取链接
    protected $connection = 'business';

    protected $name = 'payment_sheet_source';

    protected $pk = 'id';

    protected $fieldToTime = ['order_date'];
}