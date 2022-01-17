<?php
/**
 * Created by PhpStorm.
 * author: xiejiaqing
 * Note: Tired as a dog
 * Date: 2022/1/17
 * Time: 20:58
 */

namespace catchAdmin\basisinfo\tables;


use catchAdmin\basisinfo\tables\forms\Factory;
use catcher\CatchTable;

/**
 * Class ProductRegistered
 * @package catchAdmin\basisinfo\tables
 */
class ProductRegistered extends CatchTable
{
    protected function table()
    {
    }

    protected function form()
    {
        return Factory::create('productRegistered');
    }
}