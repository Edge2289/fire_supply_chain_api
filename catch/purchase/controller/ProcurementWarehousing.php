<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/2/17
 * Time: 22:17
 */

namespace catchAdmin\purchase\controller;


use catchAdmin\basisinfo\model\ProductBasicInfo;
use catchAdmin\inventory\InventoryService;
use catchAdmin\inventory\model\Inventory;
use catchAdmin\purchase\model\ProcurementWarehousingDetails;
use catchAdmin\purchase\model\PurchaseOrder as PurchaseOrderModel;
use catcher\base\CatchController;
use app\Request;
use catcher\CatchResponse;
use catchAdmin\purchase\model\ProcurementWarehousing as ProcurementWarehousingModel;

/**
 * 入库订单
 * Class ProcurementWarehousing
 * @package catchAdmin\purchase\controller
 */
class ProcurementWarehousing extends CatchController
{
    // 采购订单和入库单
    protected $purchaseOrderModel;
    protected $procurementWarehousing;
    protected $procurementWarehousingDetails;

    // 库存
    protected $inventory;

    public function __construct(
        PurchaseOrderModel            $purchaseOrderModel,
        ProcurementWarehousingModel   $procurementWarehousing,
        ProcurementWarehousingDetails $procurementWarehousingDetails,
        Inventory                     $inventory
    )
    {
        $this->purchaseOrderModel = $purchaseOrderModel;
        $this->procurementWarehousing = $procurementWarehousing;
        $this->procurementWarehousingDetails = $procurementWarehousingDetails;
        $this->inventory = $inventory;
    }

    /**
     * @return \think\response\Json
     * @throws \think\db\exception\DbException
     * @author 1131191695@qq.com
     */
    public function index()
    {
        return CatchResponse::paginate($this->procurementWarehousing->getList());
    }

    // 录入
    public function fnEntering(Request $request)
    {
        // 采购sku必填
        // 必要检查
        $params = $request->param();

        $purchaseOrderMap = $this->purchaseOrderModel->findBy($params['purchaser_order_id']);
        if (empty($purchaseOrderMap)) {
            return CatchResponse::fail("采购订单不存在");
        }
    }

    /**
     * 更新
     *
     * @param Request $request
     * @author 1131191695@qq.com
     */
    public function fnUpdate(Request $request)
    {
        // 保存
    }

    /**
     * 审核
     *
     * @param Request $request
     * @return \think\response\Json|void
     * @author 1131191695@qq.com
     */
    public function audit(Request $request)
    {
        /**
         * 审核通过的话，添加库存记录
         * 修改采购订单的数据 - 已完成
         */
        $params = $request->param();
        $data = $this->procurementWarehousing->with([
            "hasPurchaseOrder"
        ])->findBy($params['id']);
        if (!$data) {
            return CatchResponse::fail("数据不存在");
        }
        if ($data['audit_status'] == 1) {
            return CatchResponse::fail("单据已审核,无法修改");
        }

        $updateMap = [
            'audit_status' => $params['audit_status'],
            'audit_info' => $params['audit_info'],
            'audit_user_id' => request()->user()->id,
            'audit_user_name' => request()->user()->username,
        ];
        // 审核失败
        if ($params['audit_status'] == 2) {
            $b = $this->procurementWarehousing->updateBy($params['id'], $updateMap);
            if ($b) {
                return CatchResponse::success();
            }
            return CatchResponse::fail("操作失败");
        } else {
            // 审核成功
            $this->procurementWarehousing->startTrans();
            try {
                $updateMap["status"] = 1;
                $b = $this->procurementWarehousing->updateBy($params['id'], $updateMap);
                if (!$b) {
                    throw new \Exception("修改入库单失败");
                }
                $proWareDetails = $this->procurementWarehousingDetails->with(["hasProductBasicInfo"])->where("procurement_warehousing_id", $params['id'])->select();
                $inventoryMap = [];
                foreach ($proWareDetails as $proWareDetail) {
                    // 入库
                    $inventoryMap[] = [
                        'warehouse_id' => $data['warehouse_id'],
                        'product_id' => $proWareDetail['product_id'],
                        'batch_number' => $proWareDetail['batch_number'], // 批号
                        'number' => $proWareDetail['number'], // 数量
                        'supplier_id' => $data['hasPurchaseOrder']["supplier_id"] ?? 0,
                        'factory_id' => $proWareDetail['hasProductBasicInfo']['factory_id'],
                    ];
                }
                $i = $this->inventory->insertAll($inventoryMap);
                if ($i) {
                    throw new \Exception("添加库存有误");
                }
                // 提交事务
                $this->procurementWarehousing->commit();
                return CatchResponse::success();
            } catch (\Exception $exception) {
                $this->procurementWarehousing->rollback();
                return CatchResponse::fail($exception->getMessage());
            }
        }
    }
}