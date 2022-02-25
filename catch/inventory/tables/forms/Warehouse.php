<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/1/11
 * Time: 21:41
 */

namespace catchAdmin\inventory\tables\forms;


use catcher\library\form\Form;

/**
 * Class warehouse
 * @package catchAdmin\inventory\tables\forms
 */
class Warehouse extends Form
{
    public function fields(): array
    {
        return [
            self::input("warehouse_name", "仓库名称")->required(),
            self::input("warehouse_code", "仓库编号")->required(),
            self::select("warehouse_type", "仓库类型")
                ->options(
                    self::options()->add('待检库', 1)
                        ->add('合格库', 2)
                        ->add('不合格库', 3)
                        ->add('非医疗器械库', 4)->render()
                )
                ->required()
                ->style(['width' => '100%'])
                ->allowCreate(true)
                ->filterable(true)
                ->clearable(true),
            self::input("address", "地址"),
            self::input("contact", "联系人"),
            self::input("contact_phone", "联系电话"),
            self::input("note", "备注"),
        ];
    }
}