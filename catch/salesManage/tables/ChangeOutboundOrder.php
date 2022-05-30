<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/3/20
 * Time: 19:46
 */

namespace catchAdmin\salesManage\tables;


use catchAdmin\salesManage\tables\forms\Factory;
use catcher\CatchTable;

/**
 * Class ChangeOutboundOrder
 * @package catchAdmin\salesManage\tables
 */
class ChangeOutboundOrder extends CatchTable
{
    protected function table()
    {
        return $this->getTable('ChangeOutboundOrder')
            ->withApiRoute('outboundOrder')
            ->selectionChange()
            ->render();
    }

    protected function form()
    {
        return Factory::create('ChangeOutboundOrder');
    }
}