<?php
/**
 * Created by PhpStorm.
 * author: xiejiaqing
 * Note: Tired as a dog
 * Date: 2022/1/17
 * Time: 20:57
 */

namespace catchAdmin\basisinfo\tables;


use catchAdmin\basisinfo\tables\forms\Factory;
use catcher\CatchTable;

/**
 * Class ProductDistributionInfo
 * @package catchAdmin\basisinfo\tables
 */
class ProductDistributionInfo extends CatchTable
{

    protected function table()
    {
    }

    protected function form()
    {
        return Factory::create('productDistributionInfo');
    }
}