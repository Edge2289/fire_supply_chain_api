<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/3/20
 * Time: 19:46
 */

namespace catchAdmin\salesManage\tables\forms;


use catchAdmin\basisinfo\model\CustomerInfo;
use catchAdmin\basisinfo\model\SupplierLicense;
use catchAdmin\inventory\model\Warehouse;
use catchAdmin\permissions\model\Users;
use catchAdmin\salesManage\controller\SalesOrder;
use catchAdmin\salesManage\model\SalesOrderModel;
use catcher\library\form\Form;

/**
 * Class ChangeOutboundOrder
 * @package catchAdmin\salesManage\tables\forms
 */
class ChangeOutboundOrder extends Form
{
    private $salesOrder;
    private $customerInfo;
    private $warehouse;

    public function __construct(
        SalesOrder   $salesOrder,
        Warehouse    $warehouse,
        CustomerInfo $customerInfo
    )
    {
        $this->salesOrder = $salesOrder;
        $this->customerInfo = $customerInfo;
        $this->warehouse = $warehouse;
    }

    public function fields(): array
    {
        return [
            self::date("outbound_time", "销售日期")->col(12)->required(),
            self::select("outbound_man_id", "出库人员")
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
            self::select("sales_order_id", "销售订单")
                ->options(
                    $this->salesOrder->getOutOrder()
                )->col(12)->required()->clearable(true),
            self::input("logistics_code", "物流公司")->col(12),
            self::input("logistics_number", "物流单号")->col(12),
            self::textarea("remark", "备注")
        ];
    }
}