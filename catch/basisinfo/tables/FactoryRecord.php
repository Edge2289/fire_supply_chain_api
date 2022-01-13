<?php
/**
 * Created by PhpStorm.
 * author: xiejiaqing
 * Note: Tired as a dog
 * Date: 2022/1/13
 * Time: 20:27
 */

namespace catchAdmin\basisinfo\tables;


use catchAdmin\basisinfo\tables\forms\Factory;
use catcher\CatchTable;

/**
 * Class FactoryRecord
 * @package catchAdmin\basisinfo\tables
 */
class FactoryRecord extends CatchTable
{
    protected function table()
    {
        return $this->getTable('factoryRecord')
            ->withApiRoute('basisinfo')
            ->selectionChange()
            ->render();
    }

    protected function form()
    {
        return Factory::create('factoryRecord');
    }
}