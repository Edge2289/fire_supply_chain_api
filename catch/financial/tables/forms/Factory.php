<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/2/20
 * Time: 20:14
 */

namespace catchAdmin\financial\tables\forms;


use catcher\library\form\FormFactory;

/**
 * Class Factory
 * @package catchAdmin\financial\tables\forms
 */
class Factory extends FormFactory
{
    public static function from(): string
    {
        return __NAMESPACE__;
    }
}