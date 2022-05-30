<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/2/21
 * Time: 21:14
 */

namespace catchAdmin\financial\tables\forms;


use catchAdmin\basisinfo\model\CustomerInfo;
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
            self::select("customer_info_id", "客户")
                ->options(
                    app(CustomerInfo::class)->getFormLier()
                )->col(8)->clearable(true),
            self::date("payment_time", "时间")->col(8)->required(),
            self::input("receivable_code", "单据编号")->disabled(true)->col(8),
            self::input("other", "备注")->required(),
        ];
    }
}