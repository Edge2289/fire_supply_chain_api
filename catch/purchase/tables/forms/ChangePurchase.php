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
                    // 获取自身公司下的员工
                    self::options()->add('待检库', "1")
                        ->add('合格库', 2)
                        ->add('不合格库', 3)
                        ->add('非医疗器械库', 4)->render()
                )->col(8)->required(),
            self::select("supplier_id", "供应商")
                ->options(
                    // 从供应商数据表读取
                    self::options()->add('待检库', "1")
                        ->add('合格库', 2)
                        ->add('不合格库', 3)
                        ->add('非医疗器械库', 4)->render()
                )->col(8)->required(),
            self::select("settlement_status", "结算类型")
                ->options(
                    self::options()->add('现结', "0")
                        ->add('月结', "1")->render()
                )->col(8)->required(),
        ];
    }
}