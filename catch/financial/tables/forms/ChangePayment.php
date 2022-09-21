<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/2/21
 * Time: 21:14
 */

namespace catchAdmin\financial\tables\forms;


use catchAdmin\basisinfo\model\SupplierLicense;
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
            self::select("supplier_id", "供应商")
                ->options(
                    app(SupplierLicense::class)->getSupplier()
                )->col(8)->clearable(true)->required()->appendEmit('change'),
            self::date("payment_time", "时间")->editable(true)->col(8)->required(),
            self::input("payment_code", "单据编号")->disabled(true)->col(8),
            self::input("other", "备注")->required(),
        ];
    }
}