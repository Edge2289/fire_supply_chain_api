<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/2/20
 * Time: 20:02
 */

namespace catchAdmin\financial\model;


use catcher\base\CatchModel;

/**
 * Class Invoice
 * @package catchAdmin\financial\model
 */
class Invoice extends CatchModel
{
    // 这个根据登陆的账号去获取链接
    protected $connection = 'business';

    protected $name = 'invoice';

    protected $pk = 'id';
}