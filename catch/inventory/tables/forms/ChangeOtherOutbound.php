<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/4/6
 * Time: 20:10
 */

namespace catchAdmin\inventory\tables\forms;


use catchAdmin\basisinfo\model\CustomerInfo;
use catchAdmin\inventory\model\Warehouse;
use catcher\library\form\Form;

/**
 * Class ChangeOtherOutbound
 * @package catchAdmin\inventory\tables\forms
 */
class ChangeOtherOutbound extends Form
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
            self::date("outbound_time", "出库日期")->editable(true)->col(12)->required(),
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
            self::textarea("remark", "备注")
        ];
    }
}