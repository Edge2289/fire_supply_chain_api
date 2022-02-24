<?php
/**
 * Created by PhpStorm.
 * author: xiejiaqing
 * Note: Tired as a dog
 * Date: 2022/2/20
 * Time: 20:02
 */

namespace catchAdmin\financial\model;


use catcher\base\CatchModel;

/**
 * Class Receivable
 * @package catchAdmin\financial\model
 */
class ReceivableMany extends CatchModel
{
    // 这个根据登陆的账号去获取链接
    protected $connection = 'business';

    protected $name = 'receivable_sheet';

    protected $pk = 'id';

    public function getReceivableTimeAttr($value)
    {
        return $this->toDate($value);
    }

}