<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/2/25
 * Time: 10:07
 */

namespace catchAdmin\purchase\tables;


use catchAdmin\purchase\tables\forms\Factory;
use catcher\CatchTable;

/**
 * Class ChangeProcurementWarehousing
 * @package catchAdmin\purchase\tables
 */
class ChangeProcurementWarehousing extends CatchTable
{
    protected function table()
    {
        return $this->getTable('changeProcurementWarehousing')
            ->withApiRoute('procurementWarehousing')
            ->selectionChange()
            ->render();
    }

    protected function form()
    {
        return Factory::create('changeProcurementWarehousing');
    }
}