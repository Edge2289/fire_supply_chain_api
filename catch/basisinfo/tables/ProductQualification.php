<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/1/17
 * Time: 20:57
 */

namespace catchAdmin\basisinfo\tables;


use catchAdmin\basisinfo\tables\forms\Factory;
use catcher\CatchTable;

/**
 * Class ProductQualification
 * @package catchAdmin\basisinfo\tables
 */
class ProductQualification extends CatchTable
{

    protected function table()
    {
    }

    protected function form()
    {
        return Factory::create('ProductQualification');
    }
}