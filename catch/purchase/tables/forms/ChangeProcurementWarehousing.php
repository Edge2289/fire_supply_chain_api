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
    private $users;
    private $warehouse;
    private $purchaseOrder;

    public function __construct(
        Users         $users,
        Warehouse     $warehouse,
        PurchaseOrder $purchaseOrder
    )
    {
        $this->users = $users;
        $this->warehouse = $warehouse;
        $this->purchaseOrder = $purchaseOrder;
    }

    public function fields(): array
    {
        return [
            self::date("put_date", "入库日期")->col(12)->required(),
            self::select("put_user_id", "入库人员")
                ->options(
                // 获取自身公司下的员工
                    $this->getUser()
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

    /**
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author 1131191695@qq.com
     */
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