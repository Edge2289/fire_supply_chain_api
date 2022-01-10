<?php
/**
 * Created by PhpStorm.
 * author: xiejiaqing
 * Note: Tired as a dog
 * Date: 2022/1/8
 * Time: 17:50
 */

namespace catchAdmin\basisinfo\model;


use catcher\base\CatchModel;

/**
 * Class EquipmentClass
 * @package catchAdmin\basisinfo\model
 */
class EquipmentClass extends CatchModel
{
    protected $name = 'equipment_class';

    protected $pk = 'id';

    protected $field = [
        'id',
        'scope2017',
        'scope2002'
    ];
}