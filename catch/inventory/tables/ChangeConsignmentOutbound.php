<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/3/30
 * Time: 15:23
 */

namespace catchAdmin\inventory\tables;


use catchAdmin\inventory\tables\forms\Factory;
use catcher\CatchTable;

/**
 * Class ChangeConsignmentOutbound
 * @package catchAdmin\inventory\tables
 */
class ChangeConsignmentOutbound extends CatchTable
{

    protected function table()
    {
        
    }

    protected function form()
    {
        return Factory::create('changeConsignmentOutbound');
    }
}