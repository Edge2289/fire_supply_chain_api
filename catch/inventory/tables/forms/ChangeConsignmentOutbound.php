<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/3/30
 * Time: 15:23
 */

namespace catchAdmin\inventory\tables\forms;


use catchAdmin\basisinfo\model\CustomerInfo;
use catchAdmin\inventory\model\Warehouse;
use catcher\library\form\Form;

/**
 * Class ChangeConsignmentOutbound
 * @package catchAdmin\inventory\tables\forms
 */
class ChangeConsignmentOutbound extends Form
{
    private $customerInfo;
    private $warehouse;

    public function __construct(
        Warehouse    $warehouse,
        CustomerInfo $customerInfo
    )
    {
        $this->customerInfo = $customerInfo;
        $this->warehouse = $warehouse;
    }

    public function fields(): array
    {
        return [
            self::date("outbound_time", "出库日期")->col(12)->required(),
            self::select("salesman_id", "出库人员")
                ->options(
                // 获取自身公司下的员工
                    get_company_employees()
                )->col(12)->required(),
            self::select("warehouse_id", "仓库")
                ->options(
                    $this->warehouse->tableGetWarehouse()
                )->
                col(12)->required(),
            self::select("customer_info_id", "客户")
                ->options(
                    $this->customerInfo->getFormLier()
                )->col(12)->required()->clearable(true),
            self::textarea("remark", "备注")
        ];
    }
}