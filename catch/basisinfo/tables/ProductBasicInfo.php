<?php
/**
 * Created by PhpStorm.
 * author: xiejiaqing
 * Note: Tired as a dog
 * Date: 2022/1/16
 * Time: 17:32
 */

namespace catchAdmin\basisinfo\tables;


use catchAdmin\basisinfo\tables\forms\Factory;
use catcher\CatchTable;

/**
 * Class ProductBasicInfo
 * @package catchAdmin\basisinfo\tables
 */
class ProductBasicInfo extends CatchTable
{

    protected function table()
    {
    }

    protected function form()
    {
        return Factory::create('productBasicInfo');
    }
}