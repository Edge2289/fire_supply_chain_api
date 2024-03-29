<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/3/12
 * Time: 15:33
 */

namespace catchAdmin\sccs\tables\forms;


use catchAdmin\basisinfo\model\CustomerInfo;
use catchAdmin\basisinfo\model\SupplierLicense;
use catchAdmin\permissions\model\Users;
use catcher\library\form\Form;

/**
 * Class ChangeSalesOrder
 * @package catchAdmin\salesManage\tables\forms
 */
class ChangeSalesOrder extends Form
{
    private $customerInfo;

    public function __construct(
        CustomerInfo $customerInfo
    )
    {
        $this->customerInfo = $customerInfo;
    }

    public function fields(): array
    {
        return [
            self::date("sales_time", "单据日期")->col(8)->required(),
//            self::select("salesman_id", "销售人员")
//                ->options(
//                // 获取自身公司下的员工
//                    get_company_employees()
//                )->col(8),
            self::select("sales_type", "销售类型")
                ->options(
                    function () {
                        return [
                            [
                                'value' => "1",
                                'label' => "普通销售",
                            ],
                        ];
                    }
                )->col(8)->required(),
            self::select("settlement_status", "结算类型")
                ->options(
                    self::options()->add('现结', "0")
                        ->add('月结', "1")->render()
                )->col(8)->required(),
            self::textarea("remark", "备注")
        ];
    }
}