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
use catchAdmin\inventory\model\Inventory;
use catchAdmin\inventory\model\InventoryBatch;
use catchAdmin\inventory\model\Warehouse;
use catchAdmin\purchase\model\ProcurementWarehousingDetails;
use catchAdmin\purchase\model\PurchaseOrder as PurchaseOrderModel;
use catchAdmin\purchase\model\PurchaseOrderDetails;
use catcher\base\CatchController;
use app\Request;
use catcher\CatchResponse;
use catchAdmin\purchase\model\ProcurementWarehousing as ProcurementWarehousingModel;
use catcher\exceptions\BusinessException;
use fire\data\ChangeStatus;

/**
 * 入库订单
 * Class ProcurementWarehousing
 * @package catchAdmin\purchase\controller
 */
class ProcurementWarehousing extends CatchController
{
    // 采购订单和入库单
    protected $purchaseOrderModel;
    protected $purchaseOrderDetailsModel;
    protected $procurementWarehousing;
    protected $procurementWarehousingDetails;

    protected $warehouse;

    // 库存
    protected $inventory;
    protected $inventoryBatch;


    protected $productBasicInfo;

    public function __construct(
        PurchaseOrderModel            $purchaseOrderModel,
        PurchaseOrderDetails          $purchaseOrderDetails,
        ProcurementWarehousingModel   $procurementWarehousing,
        ProcurementWarehousingDetails $procurementWarehousingDetails,
        Inventory                     $inventory,
        InventoryBatch                $inventoryBatch,
        Warehouse                     $warehouse,
        ProductBasicInfo              $productBasicInfo
    )
    {
        $this->purchaseOrderDetailsModel = $purchaseOrderDetails;
        $this->purchaseOrderModel = $purchaseOrderModel;
        $this->procurementWarehousing = $procurementWarehousing;
        $this->procurementWarehousingDetails = $procurementWarehousingDetails;
        $this->inventory = $inventory;
        $this->inventoryBatch = $inventoryBatch;
        $this->warehouse = $warehouse;
        $this->productBasicInfo = $productBasicInfo;
    }

    /**
     * @return \think\response\Json
     * @throws \think\db\exception\DbException
     * @author 1131191695@qq.com
     */
    public function index()
    {
        $data = $this->procurementWarehousing->getList();
        foreach ($data as &$datum) {
            $warehouseData = $this->warehouse->where("id", $datum["warehouse_id"])->find();
            $datum['warehouse_name'] = $warehouseData['warehouse_name'];
        }

        ChangeStatus::getInstance()->audit()->status()->handle($data);
        return CatchResponse::paginate($data);
    }

    // 录入
    public function save(Request $request)
    {
        // 采购sku必填
        // 必要检查
        $params = $request->param();

        $purchaseOrderMap = $this->purchaseOrderModel->findBy($params['purchase_order_id']);
        if (empty($purchaseOrderMap)) {
            return CatchResponse::fail("采购订单不存在");
        }
        list($warehousingMap, $changePurchaseOrderDetails, $put_num) = $this->assemblyPurchaseOrder($params["purchase_order"]);
        unset($params["purchase_order"]);
        // 开启事务
        $this->procurementWarehousing->startTrans();
        try {
            $params['put_date'] = strtotime($params['put_date']);
            $params['put_num'] = $put_num;

            if (isset($params["id"]) && !empty($params["id"])) {
                // 存在id 更新操作
                $data = $this->procurementWarehousing->findBy($params['id']);
                if ($data['audit_status'] == 1) {
                    return CatchResponse::fail("单据已审核,无法修改");
                }
                if ($data['status'] != 0) {
                    return CatchResponse::fail("单据不为未完成,无法修改");
                }
                $this->procurementWarehousing->updateBy($params['id'], $params);
                $id = $params['id'];
                // 删除
                $this->procurementWarehousingDetails->where("procurement_warehousing_id", $params['id'])->delete();
            } else {
                $params["warehouse_entry_code"] = getCode("PW");
                $id = $this->procurementWarehousing->insertGetId($params);
                if (!$id) {
                    throw new BusinessException("添加入库单失败");
                }
            }

            foreach ($warehousingMap as &$value) {
                $value["procurement_warehousing_id"] = $id;
            }
            $this->procurementWarehousingDetails->insertAll($warehousingMap);
            $this->procurementWarehousing->commit();
            return CatchResponse::success();
        } catch (\Exception $exception) {
            $this->procurementWarehousing->rollback();
            return CatchResponse::fail($exception->getMessage());
        }
    }

    /**
     * 处理入库商品
     *
     * @param $params
     * @return array
     * @author 1131191695@qq.com
     */
    private function assemblyPurchaseOrder($params)
    {
        $warehousingMap = [];
        $changePurchaseOrderDetails = [];
        $put_num = 0;
        foreach ($params as $param) {
            if (!isset($param['product']) || empty($param['product'])) {
                throw new BusinessException("入库明细存在没有批次的商品");
            }
            foreach ($param['product'] as $product) {
                $warehousingMap[] = [
                    "purchase_order_details_id" => $param["id"],
                    "product_id" => $param["product_id"],
                    "product_sku_id" => $param["product_sku_id"] ?? 0,
                    "batch_number" => $product["batch_number"] ?? "",
                    "serial_number" => $product["serial_number"] ?? "",
                    "production_date" => $product["production_date"] ?? "",
                    "valid_until" => $product["valid_until"] ?? "",
                    "registration_number" => $product["registration_number"] ?? "",
                    "number" => $product["number"] ?? "",
                ];
                if (isset($changePurchaseOrderDetails[$param["id"]])) {
                    $changePurchaseOrderDetails[$param["id"]] += $product["number"];
                } else {
                    $changePurchaseOrderDetails[$param["id"]] = $product["number"];
                }
                $put_num += $product["number"];
            }
        }
        return [$warehousingMap, $changePurchaseOrderDetails, $put_num];
    }

    /**
     * 审核 （入库-修改采购订单入库数量）
     *
     * @param Request $request
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author 1131191695@qq.com
     */
    public function audit(Request $request)
    {
        /**
         * 审核通过的话，添加库存记录
         * 修改采购订单的数据 - 已完成
         */
        $params = $request->param();
        $data = $this->procurementWarehousing->findBy($params['id']);
        if (!$data) {
            return CatchResponse::fail("数据不存在");
        }
        if ($data['audit_status'] == 1) {
            return CatchResponse::fail("单据已审核,无法修改");
        }
        if ($data['status'] == 2) {
            return CatchResponse::fail("单据已作废,无法修改");
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
                // 检查仓库
                $warehouseData = $this->warehouse->where("id", $data['warehouse_id'])->find();
                if (empty($warehouseData)) {
                    throw new BusinessException("仓库不存在,无法审核入库");
                }

                // 获取采购单的数据，检查入库数量是否满足
                $purchaseData = $this->purchaseOrderModel->where("id", $data["purchase_order_id"])->find();
                if (empty($purchaseData)) {
                    throw new BusinessException("采购订单不存在，审核失败");
                }
                if ($purchaseData['status'] != 0) {
                    throw new BusinessException("采购订单状态不是未完成，无法入库审核");
                }
                // 获取采购订单商品数据

                $updateMap["status"] = 1;
                $proWareDetails = $this->procurementWarehousingDetails->where("procurement_warehousing_id", $params['id'])->select();

                $inventoryMap = []; // 库存明细
                $inventoryBatchMap = []; // 库存批次
                $putMap = [];

                $totalPutNumber = 0; // 总入库数量
                foreach ($proWareDetails as $proWareDetail) {
                    $totalPutNumber += $proWareDetail['number'];
                    if (isset($putMap[$proWareDetail['purchase_order_details_id']])) {
                        $putMap[$proWareDetail['purchase_order_details_id']] += $proWareDetail['number'];
                    } else {
                        $putMap[$proWareDetail['purchase_order_details_id']] = $proWareDetail['number'];
                    }
                    // 入库
                    if (isset($inventoryMap[$proWareDetail['product_sku_id']])) {
                        $inventoryMap[$proWareDetail['product_sku_id']]["number"] += $proWareDetail['number'];
                    } else {
                        $productData = $this->productBasicInfo->findBy($proWareDetail['product_id']);
                        $inventoryMap[$proWareDetail['product_sku_id']] = [
                            'product_id' => $proWareDetail['product_id'],
                            'product_sku_id' => $proWareDetail['product_sku_id'],
                            'warehouse_id' => $data['warehouse_id'],
                            'supplier_id' => $purchaseData["supplier_id"],
                            'factory_id' => $productData['factory_id'],
                            'company_id' => request()->user()->department_id,
                            'number' => $proWareDetail['number'], // 数量
                            'use_number' => 0, // 批号
                            'lock_number' => 0, // 批号
                        ];
                    }
                    $inventoryBatchMap[$proWareDetail['product_sku_id']][] = [
                        'company_id' => request()->user()->department_id,
                        'product_id' => $proWareDetail['product_id'],
                        'product_sku_id' => $proWareDetail['product_sku_id'],
                        'batch_number' => $proWareDetail['batch_number'],
                        'serial_number' => $proWareDetail['serial_number'],
                        'production_date' => $proWareDetail['production_date'],
                        'valid_until' => $proWareDetail['valid_until'],
                        'registration_number' => $proWareDetail['registration_number'],
                        'number' => $proWareDetail['number'],
                        'use_number' => 0,
                    ];
                }

                // 检查入库数量是否正常
                $purchaseOrderDetailsData = $this->purchaseOrderDetailsModel->whereIn("id", array_keys($putMap))->select();
                foreach ($purchaseOrderDetailsData as $purchaseOrderDetailsDatum) {
                    if ($putMap[$purchaseOrderDetailsDatum['id']] > ($purchaseOrderDetailsDatum["quantity"] - $purchaseOrderDetailsDatum["warehousing_quantity"])) {
                        throw new BusinessException("存在入库数量大于采购数量");
                    }
                }

                // 自增采购订单入库数量
                $this->purchaseOrderModel->where("id", $data["purchase_order_id"])->increment("put_num", $totalPutNumber);
                foreach ($putMap as $id => $value) {
                    // 修改采购订单商品入库数量
                    $this->purchaseOrderDetailsModel->where("id", $id)->increment("warehousing_quantity", $value);
                }
                $b = $this->procurementWarehousing->updateBy($params['id'], $updateMap);
                if (!$b) {
                    throw new \Exception("修改入库单失败");
                }

                foreach ($inventoryMap as $inventoryM) {
                    // 检查一下之前是否存在这个库存
                    $inventoryP = $this->inventory->where([
                        'product_sku_id' => $inventoryM['product_sku_id'],
                        'warehouse_id' => $inventoryM['warehouse_id'],
                        'supplier_id' => $inventoryM["supplier_id"],
                        'factory_id' => $inventoryM['factory_id'],
                        'company_id' => request()->user()->department_id,
                    ])->find();
                    if ($inventoryP) {
                        // 更新
                        $id = $inventoryP["id"];
                        $this->inventory->where("id", $inventoryP["id"])->increment("number", $inventoryM["number"]);
                    } else {
                        // 新增
                        $id = $this->inventory->insertGetId($inventoryM);
                    }
                    $insertMap = [];
                    foreach ($inventoryBatchMap as $value) {
                        foreach ($value as $v) {
                            $v["inventory_id"] = $id;
                            $insertMap[] = $v;
                        }
                    }
                    $this->inventoryBatch->insertAll($insertMap);
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

    /**
     * 作废
     *
     * @param Request $request
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author 1131191695@qq.com
     */
    public function invalid(Request $request)
    {
        $id = $request->param("id") ?? 0;
        $data = $this->procurementWarehousing->where("id", $id)->find();
        if (empty($data)) {
            throw new BusinessException("不存在的入库订单");
        }
        if ($data['status'] != 0) {
            throw new BusinessException("状态不等于未完成");
        }
        $this->procurementWarehousing->updateBy($id, [
            "status" => 2
        ]);
        return CatchResponse::success();
    }
}