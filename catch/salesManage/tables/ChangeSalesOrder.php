<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/3/12
 * Time: 15:33
 */

namespace catchAdmin\salesManage\tables;


use catchAdmin\salesManage\tables\forms\Factory;
use catcher\CatchTable;

/**
 * Class ChangeSalesOrder
 * @package catchAdmin\salesManage\tables
 */
class ChangeSalesOrder extends CatchTable
{
    protected function table()
    {
        return $this->getTable('ChangeSalesOrder')
            ->withApiRoute('salesOrder')
            ->selectionChange()
            ->render();
    }

    protected function form()
    {
        return Factory::create('ChangeSalesOrder');
    }
}