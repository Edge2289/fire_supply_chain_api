<?php
/**
 * Created by PhpStorm.
 * author: xiejiaqing
 * Note: Tired as a dog
 * Date: 2022/2/21
 * Time: 21:14
 */

namespace catchAdmin\financial\tables;


use catchAdmin\financial\tables\forms\Factory;
use catcher\CatchTable;

/**
 * Class ChangeReceivable
 * @package catchAdmin\financial\tables
 */
class ChangeReceivable extends CatchTable
{
    protected function table()
    {

    }

    protected function form()
    {
        return Factory::create('changeReceivable');
    }
}