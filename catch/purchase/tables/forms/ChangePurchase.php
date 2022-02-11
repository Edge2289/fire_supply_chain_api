<?php
/**
 * Created by PhpStorm.
 * author: xiejiaqing
 * Note: Tired as a dog
 * Date: 2022/2/6
 * Time: 21:05
 */

namespace catchAdmin\purchase\tables\forms;


use catcher\library\form\Form;

/**
 * Class ChangePurchase
 * @package catchAdmin\purchase\tables\forms
 */
class ChangePurchase extends Form
{
    public function fields(): array
    {
        // 获取当前公司下的人员
        // request()->user()->id
        return [
            self::date("purchase_date", "采购日期")->col(8)->required(),
            self::select("user_id", "采购人员")
                ->options(
                    self::options()->add('待检库', "1")
                        ->add('合格库', 2)
                        ->add('不合格库', 3)
                        ->add('非医疗器械库', 4)->render()
                )->col(8)->required(),
            self::select("supplier_id", "供应商")
                ->options(
                    self::options()->add('待检库', "1")
                        ->add('合格库', 2)
                        ->add('不合格库', 3)
                        ->add('非医疗器械库', 4)->render()
                )->col(8)->required(),
        ];
    }
}