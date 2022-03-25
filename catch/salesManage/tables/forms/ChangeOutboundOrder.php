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
    private $users;
    private $salesOrder;
    private $customerInfo;
    private $warehouse;

    public function __construct(
        Users        $users,
        SalesOrder   $salesOrder,
        Warehouse    $warehouse,
        CustomerInfo $customerInfo
    )
    {
        $this->users = $users;
        $this->salesOrder = $salesOrder;
        $this->customerInfo = $customerInfo;
        $this->warehouse = $warehouse;
    }

    public function fields(): array
    {
        return [
            self::date("outbound_time", "销售日期")->col(12)->required(),
            self::select("outbound_man_id", "销售人员")
                ->options(
                // 获取自身公司下的员工
                    $this->getUser()
                )->col(12)->required(),
            self::select("warehouse_id", "仓库")
                ->options(
                    $this->warehouse->tableGetWarehouse()
                )->
                col(12)->required(),
            self::select("customer_info_id", "客户")
                ->options(
                    $this->customerInfo->getFormLier()
                )->col(12)->required(),
            self::select("sales_order_id", "销售订单")
                ->options(
                    $this->salesOrder->getOutOrder()
                )->col(12)->required(),
            self::textarea("remark", "备注")
        ];
    }

    public function getUser()
    {
        $userId = request()->user()->id;
        $data = $this->users->where("id", $userId)->find();
        if (!$data['department_id']) {
            return [];
        }
        $data = $this->users->where("department_id", $data['department_id'])->select();
        $map = [];
        foreach ($data as $datum) {
            $map[] = [
                'value' => (string)$datum['id'],
                'label' => $datum['username'],
            ];
        }
        return $map;
    }
}