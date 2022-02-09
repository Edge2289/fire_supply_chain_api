<?php
/**
 * Created by PhpStorm.
 * author: xiejiaqing
 * Note: Tired as a dog
 * Date: 2022/2/6
 * Time: 21:04
 */

namespace catchAdmin\purchase\tables;


use catchAdmin\purchase\tables\forms\Factory;
use catcher\CatchTable;

/**
 * Class changePurchase
 * @package catchAdmin\purchase\tables
 */
class changePurchase extends CatchTable
{
    protected function table()
    {
        return $this->getTable('changePurchase')
            ->withApiRoute('purchase')
            ->selectionChange()
            ->render();
    }

    protected function form()
    {
        return Factory::create('changePurchase');
    }
}