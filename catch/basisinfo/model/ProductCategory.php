<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/4/11
 * Time: 17:13
 */

namespace catchAdmin\basisinfo\model;


use catcher\base\CatchModel;

/**
 * Class ProductCategory
 * @package catchAdmin\basisinfo\model
 */
class ProductCategory extends CatchModel
{
    protected $name = 'product_category';

    protected $fieldToString = ['sort'];
}