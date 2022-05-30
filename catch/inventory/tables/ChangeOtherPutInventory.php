<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/4/6
 * Time: 20:10
 */

namespace catchAdmin\inventory\tables;


use catchAdmin\inventory\tables\forms\Factory;
use catcher\CatchTable;
use catcher\library\form\Form;

/**
 * Class ChangeOtherPutInventory
 * @package catchAdmin\inventory\tables
 */
class ChangeOtherPutInventory extends CatchTable
{
    protected function table()
    {
        // TODO: Implement table() method.
    }

    protected function form()
    {
        return Factory::create('changeOtherPutInventory');
    }
}