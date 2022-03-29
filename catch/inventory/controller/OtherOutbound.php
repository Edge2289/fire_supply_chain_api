<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/3/27
 * Time: 21:38
 */

namespace catchAdmin\inventory\controller;


use catcher\base\CatchController;
use catchAdmin\inventory\model\Warehouse;

/**
 * 其他出库
 *
 * Class OtherOutbound
 * @package catchAdmin\inventory\controller
 */
class OtherOutbound extends CatchController
{
    protected $warehouse;

    public function __construct(
        Warehouse $warehouse
    )
    {
        $this->warehouse = $warehouse;
    }

    public function index()
    {

    }
}