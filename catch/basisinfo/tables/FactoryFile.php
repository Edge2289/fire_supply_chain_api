<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/1/9
 * Time: 20:59
 */

namespace catchAdmin\basisinfo\tables;


use catchAdmin\basisinfo\tables\forms\Factory;
use catcher\CatchTable;

/**
 * Class FactoryFile
 * @package catchAdmin\basisinfo\tables
 */
class FactoryFile extends CatchTable
{
    protected function table()
    {
        return $this->getTable('factoryFile')
            ->withApiRoute('basisinfo')
            ->selectionChange()
            ->render();
    }

    protected function form()
    {
        return Factory::create('factoryFile');
    }
}