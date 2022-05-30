<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/3/28
 * Time: 18:33
 */

namespace catchAdmin\financial\tables\forms;


use catchAdmin\basisinfo\model\CustomerInfo;
use catcher\library\form\Form;

/**
 * Class ChangeInvoice
 * @package catchAdmin\financial\tables\forms
 */
class ChangeInvoice extends Form
{
    public function fields(): array
    {
        return [
            self::date("invoice_time", "发票时间")->col(12)->required(),
            self::input("amount", "发票金额")->col(12)->required(),
            self::select("customer_info_id", "客户")
                ->options(
                    app(CustomerInfo::class)->getFormLier()
                )->col(12)->clearable(true),
            self::select("order_type", "订单类型")->col(12)->clearable(true)
                ->options(
                    self::options()->add('采购订单', "1")
                        ->add('出库单订单', "2")
                        ->render()
                )->required(),
            self::select("invoice_type", "发票类型")->col(12)->clearable(true)
                ->options(
                    self::options()->add('增值税普通发票', "1")
                        ->add('增值税专用发票', "2")
                        ->render()
                )->required(),
            self::textarea("other", "备注")->required()->col(12),
            self::file("附件", "attachment")->col(12),
        ];
    }
}