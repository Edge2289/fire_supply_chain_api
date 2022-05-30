<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/3/28
 * Time: 18:33
 */

namespace catchAdmin\financial\tables;


use catchAdmin\financial\tables\forms\Factory;
use catcher\CatchTable;

/**
 * Class ChangeInvoice
 * @package catchAdmin\financial\tables
 */
class ChangeInvoice extends CatchTable
{
    protected function table()
    {

    }

    protected function form()
    {
        return Factory::create('changeInvoice');
    }
}