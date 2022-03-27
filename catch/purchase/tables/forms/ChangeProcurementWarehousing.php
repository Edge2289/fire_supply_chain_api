<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/2/6
 * Time: 21:05
 */

namespace catchAdmin\purchase\tables\forms;


use catchAdmin\inventory\model\Warehouse;
use catchAdmin\permissions\model\Users;
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
            self::date("put_date", "入库日期")->col(12)->required(),
            self::select("put_user_id", "入库人员")
                ->options(
                    get_company_employees()
                )->col(12)->required(),
            self::select("warehouse_id", "仓库")
                ->options(
                    $this->warehouse->tableGetWarehouse()
                )->
                col(12)->required(),
            self::select("purchase_order_id", "采购订单")
                ->options(
                    $this->purchaseOrder->tableGetPurchaseOrderLists()
                )->
                col(12)->required(),
            self::input("delivery_code", "收货单号")->col(12)->required(),
            self::textarea("remark", "备注")->col(12)->required(),
        ];
    }
}