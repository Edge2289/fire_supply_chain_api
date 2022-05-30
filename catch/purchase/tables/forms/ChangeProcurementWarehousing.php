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
use catchAdmin\inventory\model\Warehouse;
use catchAdmin\purchase\controller\PurchaseOrder;
use catcher\library\form\Form;

/**
 * Class ChangeProcurementWarehousing
 * @package catchAdmin\purchase\tables\forms
 */
class ChangeProcurementWarehousing extends Form
{
    private $warehouse;
    private $purchaseOrder;

    public function __construct(
        Warehouse     $warehouse,
        PurchaseOrder $purchaseOrder
    )
    {
        $this->warehouse = $warehouse;
        $this->purchaseOrder = $purchaseOrder;
    }

    public function fields(): array
    {
        return [
            self::date("put_date", "单据日期")->col(8)->required(),
            self::select("put_user_id", "经手人")
                ->options(
                    get_company_employees()
                )->col(8)->required(),
            self::select("warehouse_id", "仓库")
                ->options(
                    $this->warehouse->tableGetWarehouse()
                )->
                col(8)->required(),
            self::select("supplier_id", "供应商")
                ->options(
                    app(SupplierLicense::class)->getSupplier()
                )->col(8)->required()->appendEmit('change'),
            self::select("purchase_order_id", "采购订单")
                ->options(
                    $this->purchaseOrder->tableGetPurchaseOrderLists()
                )->
                col(8)->required()->appendEmit('change'),
            self::textarea("remark", "备注"),
            self::date("inspection_date", "收/验货日期")->col(8),
            self::select("inspection_user_id", "收/验货人员")
                ->options(
                    get_company_employees()
                )->col(8)->required(),
            self::select("is_qualified", "是否合格")->options(
                self::options()->add("合格", "1")
                    ->add("不合格", "2")->render()
            )->col(8),
            self::select("logistics_info", "物流信息")->options(
                self::options()->add("快递", "1")
                    ->add("送货", "2")
                    ->add("自提", "3")->render()
            )->col(8)->control([
                [
                    "value" => "1",
                    "rule" => [
                        self::select("courier_company", "快递公司")->options(
                            self::options()->add("申通快递", "shentong")
                                ->add("百世快递", "baishi")
                                ->add("顺丰快递", "shunfen")->render()
                        )->col(8)->required(),
                        self::input("courier_code", "快递单号")->col(8)->required()
                    ]
                ], [
                    "value" => "2",
                    "rule" => [
                        self::input("contact_name", "联系人")->col(8)->required(),
                        self::input("phone", "电话")->col(8)->required(),
                    ]
                ], [
                    "value" => "3",
                    "rule" => [
                        self::input("contact_name", "联系人")->col(8)->required(),
                        self::input("phone", "电话")->col(8)->required(),
                    ]
                ]
            ])->required(),
            self::file("供应商附件", "attachment")
        ];
    }
}