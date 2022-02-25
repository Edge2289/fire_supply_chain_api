<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/2/21
 * Time: 21:14
 */

namespace catchAdmin\financial\tables\forms;


use catcher\library\form\Form;

/**
 * Class ChangeReceivable
 * @package catchAdmin\financial\tables\forms
 */
class ChangePayment extends Form
{
    public function fields(): array
    {
        return [
            self::date("payment_time", "付款时间")->required(),
//            self::input("receivable_code", "付款单号")->disabled(true)->col(12),
            self::input("amount", "付款金额")->col(12)->required(),
            self::select("payment_type", "付款类型")->col(12)->clearable(true)
                ->options(
                    self::options()->add('常规', "1")
                        ->add('现金', "2")
                        ->add('尾款', "3")
                        ->add('保证金', "4")
                        ->add('其他', "5")
                        ->render()
                )->required(),
            self::select("payment_method", "支付方式")->col(12)->clearable(true)
                ->options(
                    self::options()->add('银行转账', "1")
                        ->add('现金', "2")
                        ->add('其他', "3")
                        ->render()
                )->required(),
            self::file("附件", "attachment")->col(12),
            self::input("other", "备注")->required(),
        ];
    }
}