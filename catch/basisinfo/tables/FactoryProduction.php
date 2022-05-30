<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/1/13
 * Time: 20:13
 */

namespace catchAdmin\basisinfo\tables;


use catchAdmin\basisinfo\tables\forms\Factory;
use catcher\CatchTable;

/**
 * Class FactoryProduction
 * @package catchAdmin\basisinfo\tables
 */
class FactoryProduction extends CatchTable
{
    protected function table()
    {
        return $this->getTable('factoryProduction')
            ->withApiRoute('basisinfo')
            ->selectionChange()
            ->render();
    }

    protected function form()
    {
        return Factory::create('factoryProduction');
    }
}