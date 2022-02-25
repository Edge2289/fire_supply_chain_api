<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/2/14
 * Time: 22:21
 */

namespace catchAdmin\basisinfo\tables;


use catchAdmin\basisinfo\tables\forms\Factory;
use catcher\CatchTable;

/**
 * Class CustomerHospital
 * @package catchAdmin\basisinfo\tables
 */
class CustomerHospital extends CatchTable
{
    protected function table()
    {
    }

    protected function form()
    {
        return Factory::create('customerHospital');
    }
}