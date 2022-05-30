<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/1/17
 * Time: 20:59
 */

namespace catchAdmin\basisinfo\tables;


use catchAdmin\basisinfo\tables\forms\Factory;
use catcher\CatchTable;

/**
 * Class ProductRecord
 * @package catchAdmin\basisinfo\tables
 */
class ProductRecord extends CatchTable
{

    protected function table()
    {
    }

    protected function form()
    {
        return Factory::create('productRecord');
    }
}