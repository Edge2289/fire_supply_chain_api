<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/2/6
 * Time: 21:05
 */

namespace catchAdmin\purchase\tables\forms;


use catchAdmin\basisinfo\model\SupplierLicense;
use catchAdmin\permissions\model\Users;
use catcher\library\form\Form;

/**
 * Class ChangePurchase
 * @package catchAdmin\purchase\tables\forms
 */
class ChangePurchase extends Form
{
    private $supplier;

    public function __construct(
        SupplierLicense $supplier
    )
    {
        $this->supplier = $supplier;
    }

    public function fields(): array
    {
        return [
            self::date("purchase_date", "单据日期")->editable(true)->col(12)->required(),
            self::select("user_id", "采购人员")
                ->options(
                    get_company_employees()
                )->col(12),
            self::select("supplier_id", "供应商")
                ->options(
                    $this->supplier->getSupplier()
                )->col(12)->required(),
            self::select("settlement_status", "结算类型")
                ->options(
                    self::options()->add('现结', "0")
                        ->add('月结', "1")->render()
                )->col(12)->required(),
            self::textarea("remark", "备注")->col(12)
        ];
    }
}