<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/3/6
 * Time: 22:54
 */

namespace catchAdmin\salesManage\controller;


use catchAdmin\basisinfo\model\SupplierLicense;
use catchAdmin\inventory\model\ConsignmentOutbound;
use catchAdmin\inventory\model\ConsignmentOutboundDetails;
use catchAdmin\inventory\model\ReadyOutbound;
use catchAdmin\inventory\model\ReadyOutboundDetails;
use catchAdmin\inventory\model\TurnSalesRecord;
use catchAdmin\salesManage\model\SalesOrderDetailsModel;
use catchAdmin\salesManage\model\SalesOrderModel;
use catcher\base\CatchController;
use catcher\CatchResponse;
use catcher\exceptions\BusinessException;
use catcher\Utils;
use fire\data\ChangeStatus;
use think\db\exception\DbException;
use think\Request;
use think\response\Json;

/**
 * Class SalesOrder
 * @package catchAdmin\salesManage\controller
 */
class SalesOrder extends CatchController
{
    protected $salesOrderModel;
    protected $salesOrderDetailsModel;

    public function __construct(
        SalesOrderModel        $salesOrderModel,
        SalesOrderDetailsModel $salesOrderDetailsModel
    )
    {
        $this->salesOrderModel = $salesOrderModel;
        $this->salesOrderDetailsModel = $salesOrderDetailsModel;
    }

    /**
     * @return Json
     * @throws DbException
     * @author 1131191695@qq.com
     */
    public function index()
    {
        $data = $this->salesOrderModel->getList();
        foreach ($data as &$datum) {
            $datum['settlement_status_i'] = $datum['settlement_status'] == 0 ? "未结" : "已结";
            $datum['settlement_type_i'] = $datum['settlement_type'] == 0 ? "现结" : "月结";
        }
        ChangeStatus::getInstance()->audit()->status()->handle($data);
        return CatchResponse::paginate($data);
    }

    public function save(Request $request)
    {
        return $this->insert($request->param());
    }

    /**
     * 保存或者更新
     *
     * @param $params
     * @return Json
     * @author 1131191695@qq.com
     */
    public function insert($params)
    {
        // 保存基础信息
        $goodsDetails = $params['goods_details'];
        unset($params['goods_details']);
        $this->salesOrderModel->startTrans();
        try {
            if (empty($params['salesman_id'])) {
                $params['salesman_id'] = \request()->user()->id;
            }
            $params['sales_time'] = strtotime($params['sales_time']);
            if (isset($params["id"]) && !empty($params["id"])) {
                // 存在id 更新操作
                $data = $this->salesOrderModel->findBy($params['id']);
                if ($data['audit_status'] == 1) {
                    return CatchResponse::fail("单据已审核,无法修改");
                }
                if ($data['status'] != 0) {
                    return CatchResponse::fail("单据不为未完成,无法修改");
                }
                $this->salesOrderModel->updateBy($params['id'], $params);
                $id = $params['id'];
                // 删除
                $this->salesOrderDetailsModel->destroy(["sales_order_id" => $params['id']]);
            } else {
                $params['order_code'] = getCode("SO");
                $params['company_id'] = request()->user()->department_id;
                unset($params['id']);
                $id = $this->salesOrderModel->insertGetId($params);
                if (!$id) {
                    throw new BusinessException("销售订单添加失败");
                }
            }
            // 重新添加商品数据
            $skuIds = [];
            $map = [];
            $totalNum = 0;
            $totalPrice = 0;
            foreach ($goodsDetails as $goodsDetail) {
                if (in_array($goodsDetail['id'], $skuIds)) {
                    throw new BusinessException("商品数据重复");
                }
                if (empty($goodsDetail['entity']) || empty($goodsDetail['unit_price'])) {
                    throw new BusinessException("商品数据填写不完整");
                }
                $skuIds[] = $goodsDetail['id'];
                $map[] = [
                    'sales_order_id' => $id,
                    'product_id' => $goodsDetail['product_id'] ?? 0,
                    'product_sku_id' => $goodsDetail['id'],
                    'product_code' => $goodsDetail['product_code'],
                    'item_number' => $goodsDetail['item_number'],
                    'entity' => $goodsDetail['entity'],
                    'sku_code' => $goodsDetail['sku_code'],
                    'unit_price' => $goodsDetail['unit_price'],
                    'tax_rate' => Utils::config('product.tax'),
                    'quantity' => $goodsDetail['quantity'],
                    'delivery_number' => 0,
                    'note' => $goodsDetail['note'] ?? "",
                ];
                $totalNum += $goodsDetail['quantity'];
                $totalPrice += $goodsDetail['unit_price'];
            }
            if (empty($map)) {
                throw new BusinessException("商品数据为空");
            }
            $gId = $this->salesOrderDetailsModel->insertAll($map);
            if (empty($gId)) {
                throw new \Exception("销售订单商品添加失败");
            }
            $this->salesOrderModel->updateBy($id, [
                'num' => $totalNum,
                'amount' => $totalPrice,
            ]);
            // 提交事务
            $this->salesOrderModel->commit();
        } catch (\Exception $exception) {
            // 回滚事务
            $this->salesOrderModel->rollback();
            throw new BusinessException($exception->getMessage());
        }
        return CatchResponse::success(['id' => $id]);
    }

    /**
     * 审核
     *
     * @param Request $request
     * @return Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author 1131191695@qq.com
     */
    public function audit(Request $request)
    {
        $data = $request->param();
        $salesOrderData = $this->salesOrderModel->findBy($data['id']);
        if (empty($salesOrderData)) {
            throw new BusinessException("不存在销售订单");
        }
        if ($salesOrderData['audit_status'] == 1) {
            throw new BusinessException("已审核");
        }
        $b = $this->salesOrderModel->updateBy($data['id'], [
            'audit_status' => $data['audit_status'],
            'audit_info' => $data['audit_info'],
            'audit_user_id' => request()->user()->id,
            'audit_user_name' => request()->user()->username,
        ]);
        if ($b) {
            return CatchResponse::success();
        }
        return CatchResponse::fail("操作失败");
    }

    /**
     * 作废
     *
     * @param Request $request
     * @return Json
     * @author 1131191695@qq.com
     */
    public function invalid(Request $request)
    {
        $data = $request->param();
        $this->salesOrderModel->startTrans();
        try {
            $data = $this->salesOrderModel->getFindByKey($data['id']);
            if ($data['audit_status'] != 0) {
                throw new BusinessException("订单已审核，无法作废");
            }
            if ($data['status'] != 0) {
                throw new BusinessException("订单状态不为未完成，无法作废");
            }
            $this->salesOrderModel->updateBy($data['id'], [
                'status' => 2
            ]);
            // 恢复转销售来源的订单转销售数量
            if (in_array($data['sales_type'], [2, 3])) {
                // 获取转销售记录表
                if ($data['sales_type'] == 2) {
                    // 寄售出库
                    $formModel = app(ConsignmentOutbound::class);
                    $formDetailsModel = app(ConsignmentOutboundDetails::class);
                } else {
                    // 备货出库
                    $formModel = app(ReadyOutbound::class);
                    $formDetailsModel = app(ReadyOutboundDetails::class);
                }
                $turnSalesRecordData = app(TurnSalesRecord::class)->where('sales_order_id', $data['id'])->select();
                foreach ($turnSalesRecordData as $turnSalesRecordDatum) {
                    // 减少对应的订单转销售数
                    $formModel->where("id", $turnSalesRecordDatum['form_id'])->decrement("inventory_quantity", $turnSalesRecordDatum['quantity']);
                    $formDetailsModel->where("id", $turnSalesRecordDatum['form_details_id'])->decrement("inventory_quantity", $turnSalesRecordDatum['quantity']);
                }
            }
            $this->salesOrderModel->commit();
        } catch (\Exception $exception) {
            $this->salesOrderModel->rollback();
            throw new BusinessException($exception->getMessage());
        }
        return CatchResponse::success();
    }

    /**
     * 获取满足条件的订单数据
     *
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author 1131191695@qq.com
     */
    public function getOutOrder()
    {
        $data = $this->salesOrderModel->alias("so")
            ->field(["so.id", "so.order_code"])
            ->where("so.audit_status", 1) // 已审核
            ->where("so.status", 0) // 未完成
            ->where(function ($query) {
                $query->where([
                    "so.settlement_type" => 0,
                    "so.settlement_status" => 1,
                ])->whereOr([
                    "so.settlement_type" => 1,
                    "so.settlement_status" => 0,
                ]);
            })
            ->whereRaw("(so.num - so.put_num) > 0")
            ->order("so.id", "desc")
            ->group("so.id")
            ->select()->toArray();
        $map = [];
        foreach ($data as $datum) {
            $map[] = [
                "label" => $datum['order_code'],
                "value" => (string)$datum['id'],
            ];
        }
        return $map;
    }

    /**
     * 出库的商品数据
     *
     * @param Request $request
     * @return Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author 1131191695@qq.com
     */
    public function outboundOrder(Request $request)
    {
        $params = $request->param();
        if (empty($params)) {
            throw new BusinessException("参数缺失");
        }
        $data = $this->salesOrderModel->with(["hasSalesOrderDetails"])
            ->where("audit_status", 1) // 已审核
            ->where("status", 0) // 未完成
            ->whereRaw("(num - put_num) > 0")
            ->when(!empty($params), function ($query) use ($params) {
                if (!empty($params['customer_id'])) {
                    $query->where('customer_info_id', $params['customer_id']);
                }
                if (!empty($params['sales_order_id'])) {
                    $query->where('id', $params['sales_order_id']);
                }
            })
            ->where(function ($query) {
                $query->where([
                    "settlement_type" => 0,
                    "settlement_status" => 1,
                ])->whereOr([
                    "settlement_type" => 1,
                    "settlement_status" => 0,
                ]);
            })->find();
        if (empty($data)) {
            throw new BusinessException("订单条件不满足，无法出库");
        }
        $goodsDetails = [];
        // 获取出库订单控制器
        $outboundOrderController = app(OutboundOrder::class);
        foreach ($data['hasSalesOrderDetails'] as $hasSalesOrderDetail) {
            $details = [
                'sales_order_details_id' => $hasSalesOrderDetail['id'],
                'product_name' => $hasSalesOrderDetail->hasProductData['product_name'],
                "quantity" => $hasSalesOrderDetail["quantity"],
                "outbound_quantity" => bcsub($hasSalesOrderDetail["quantity"], $hasSalesOrderDetail["delivery_number"]),
                "selectedNumber" => 0,
                "selectedBatchNumber" => 0,
            ];
            list($assemblyDetailsData, $detail) = $this->salesOrderModel->assemblyDetailsData($hasSalesOrderDetail);
            $details = array_merge($details, $assemblyDetailsData);
            // 存在销售数据
            if (isset($params['outbound_order_id'])) {
                // selectOutboundItem
                list($map, $selectedNumber, $selectedBatchNumber) = $outboundOrderController->getSelectOutboundOrder($params['outbound_order_id'], $hasSalesOrderDetail->hasProductSkuData['id']);
                $details['selectOutboundItem'] = $map;
                $details['selectedNumber'] = $selectedNumber;
                $details['selectedBatchNumber'] = $selectedBatchNumber;
                $details['outbound_quantity'] = ($hasSalesOrderDetail["quantity"] - ($hasSalesOrderDetail["delivery_number"] - $selectedNumber));
            }
            $entityName = '';
            $zhuijia = [];
            foreach ($details['hasProductEntity'] as $i => $xx) {
                if ($i == 0) {
                    $entityName = $xx['deputy_unit_name'];
                    continue;
                }
                $zhuijia[] = "({$xx['deputy_unit_name']}={$xx['proportion']})";
            }
            $details['entityName'] = $entityName . implode('/', $zhuijia);
            $goodsDetails[] = $details;
        }
        return CatchResponse::success($this->fillTurnSalesData([
            'sales_order_id' => $data['id'],
            'sales_type' => $data['sales_type'],
            'customer_info_id' => $data['customer_info_id'],
            'company_id' => $data['company_id'],
            'warehouse_id' => 0,
            'goodsDetails' => $goodsDetails
        ]));
    }

    protected function fillTurnSalesData(array $data): array
    {
        if (!in_array($data['sales_type'], [2, 3])) {
            return $data;
        }
        $selectedNumber = 0;
        foreach ($data['goodsDetails'] as &$goodsDetail) {
            $iMap = [];
            $turnSalesRecordData = app(TurnSalesRecord::class)->with([
                'hasInventoryBatch', 'hasProductData', 'hasProductSkuData'
            ])->where("product_sku_id", $goodsDetail['product_sku_id'])
                ->where('sales_order_id', $data['sales_order_id'])->select();
            foreach ($turnSalesRecordData as $key => $datum) {
                $data['warehouse_id'] = $datum['warehouse_id'];
                $iMap[$key] = $datum['hasInventoryBatch'];
                $iMap[$key]["product_name"] = $datum["hasProductData"]["product_name"] ?? '';
                $iMap[$key]["product_sku_name"] = $datum["hasProductSkuData"]["sku_code"] ?? '';
                $iMap[$key]['out_number'] = $datum['quantity'];
                $selectedNumber += $datum['quantity'];
            }
            $goodsDetail['selectOutboundItem'] = $iMap;
            $goodsDetail['selectedNumber'] = $selectedNumber;
            $goodsDetail['selectedBatchNumber'] = count($turnSalesRecordData);
            $goodsDetail['outbound_quantity'] = 0; // 出库数量
        }
        return $data;
    }

    /**
     * 获取弹窗选择收款单的销售订单
     *
     * @param Request $request
     * @return Json
     * @throws DbException
     * @author 1131191695@qq.com
     */
    public function getAlertOrder(Request $request)
    {
        $params = $request->param();
        if (!isset($params['page']) || empty($params['page'])) {
            $params['page'] = 1;
        }
        if (!isset($params['pageSize']) || empty($params['pageSize'])) {
            $params['pageSize'] = 10;
        }
        $queryModel = $this->salesOrderModel;
        $data = $queryModel->where("status", 0)
            ->with([
                "hasCustomerInfo" => function ($query) {
                    $query->field(["id", "company_name"]);
                }
            ])
            ->where("audit_status", 1)
            ->where(function ($query) {
                $query->where([
                    "settlement_type" => 0,
                    "settlement_status" => 1,
                ])->whereOr([
                    "settlement_type" => 1,
                    "settlement_status" => 0,
                ]);
            })
            ->paginate();
        return CatchResponse::success([
            "data" => $data,
            "supple" => app(SupplierLicense::class)->getSupplier()
        ]);
    }
}