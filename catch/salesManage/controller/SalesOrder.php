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
use catchAdmin\salesManage\model\SalesOrderDetailsModel;
use catchAdmin\salesManage\model\SalesOrderModel;
use catcher\base\CatchController;
use catcher\CatchResponse;
use catcher\exceptions\BusinessException;
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
        // 保存商品
        $goodsDetails = $params['goods_details'];
        unset($params['goods_details']);
        $this->salesOrderModel->startTrans();
        try {
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
                $this->salesOrderModel->where("procurement_warehousing_id", $params['id'])->delete();
            } else {
                $params['order_code'] = getCode("SO");
                $params['sales_time'] = strtotime($params['sales_time']);
                $params['company_id'] = request()->user()->department_id;
                unset($params['id']);
                $id = $this->salesOrderModel->insertGetId($params);
                if (!$id) {
                    throw new BusinessException("销售订单添加失败");
                }
            }
            $totalNum = 0;
            $totalPrice = 0;
            // 重新添加商品数据
            $skuIds = [];
            $map = [];
            foreach ($goodsDetails as $goodsDetail) {
                if (in_array($goodsDetail['id'], $skuIds)) {
                    throw new BusinessException("商品数据重复");
                }
                $skuIds[] = $goodsDetail['id'];
                $totalNum += $goodsDetail['quantity'];
                $totalPrice = bcadd($totalPrice, bcmul($goodsDetail['unit_price'], $goodsDetail['quantity'], 2), 2);
                $map[] = [
                    'sales_order_id' => $id,
                    'product_id' => $goodsDetail['product_id'] ?? 0,
                    'product_sku_id' => $goodsDetail['id'],
                    'product_code' => $goodsDetail['product_code'],
                    'item_number' => $goodsDetail['item_number'],
                    'sku_code' => $goodsDetail['sku_code'],
                    'unit_price' => $goodsDetail['unit_price'],
                    'tax_rate' => $goodsDetail['tax_rate'],
                    'quantity' => $goodsDetail['quantity'],
                    'delivery_number' => 0,
                    'note' => $goodsDetail['note'] ?? "",
                ];
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
                'amount' => (string)$totalPrice,
            ]);
            // 提交事务
            $this->salesOrderModel->commit();
        } catch (\Exception $exception) {
            // 回滚事务
            $this->salesOrderModel->rollback();
            return CatchResponse::fail($exception->getMessage());
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

    // 出库 添加相对应的出库单
    public function outbound(Request $request)
    {
        $data = $request->param();
        try {
            $this->salesOrderModel->startTrans();
            $salesOrderData = $this->salesOrderModel->with([
                "hasSalesOrderDetails"
            ])->where("id", $data["id"])->lock(true)->find();

            if ($salesOrderData['settlement_type'] == 0 && $salesOrderData["settlement_status"]) {
                throw new BusinessException("销售订单结算类型为现结，结算状态为未结");
            }

            if ($salesOrderData['audit_status'] != 1) {
                throw new BusinessException("销售订单未审核或者审核失败");
            }

            if ($salesOrderData['status'] != 0) {
                throw new BusinessException("销售订单已完成或者已作废");
            }

            if (empty($salesOrderData["hasSalesOrderDetails"])) {
                throw new BusinessException("销售订单商品数据为空");
            }

            $this->salesOrderModel->commit();
        } catch (\Exception $exception) {
            $this->salesOrderModel->rollback();
        }
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
            throw new BusinessException("当前订单无法出库");
        }
        $goodsDetails = [];
        // 获取出库订单控制器
        $outboundOrderController = app(OutboundOrder::class);
        foreach ($data['hasSalesOrderDetails'] as $hasSalesOrderDetail) {
            $details = [
                'sales_order_details_id' => $hasSalesOrderDetail['id'],
                'product_id' => $hasSalesOrderDetail->hasProductSkuData['product_id'],
                'product_code' => $hasSalesOrderDetail->hasProductSkuData['product_code'],
                'product_sku_id' => $hasSalesOrderDetail->hasProductSkuData['id'],
                'sku_code' => $hasSalesOrderDetail->hasProductSkuData['sku_code'],
                'item_number' => $hasSalesOrderDetail->hasProductSkuData['item_number'],
                'unit_price' => $hasSalesOrderDetail->hasProductSkuData['unit_price'],
                'tax_rate' => $hasSalesOrderDetail->hasProductSkuData['tax_rate'],
                'n_tax_price' => $hasSalesOrderDetail->hasProductSkuData['n_tax_price'],
                'packing_size' => $hasSalesOrderDetail->hasProductSkuData['packing_size'],
                'packing_specification' => $hasSalesOrderDetail->hasProductSkuData['packing_specification'],
                'product_name' => $hasSalesOrderDetail->hasProductData['product_name'],
                'udi' => $hasSalesOrderDetail->hasProductSkuData['udi'],
                'entity' => $hasSalesOrderDetail->hasProductSkuData['entity'],
                "quantity" => $hasSalesOrderDetail["quantity"],
                "outbound_quantity" => bcsub($hasSalesOrderDetail["quantity"], $hasSalesOrderDetail["delivery_number"]),
                "selectedNumber" => 0,
                "selectedBatchNumber" => 0,
            ];
            // 存在销售数据
            if (isset($params['outbound_order_id'])) {
                // selectOutboundItem
                list($map, $selectedNumber, $selectedBatchNumber) = $outboundOrderController->getSelectOutboundOrder($params['outbound_order_id'], $hasSalesOrderDetail->hasProductSkuData['id']);
                $details['selectOutboundItem'] = $map;
                $details['selectedNumber'] = $selectedNumber;
                $details['selectedBatchNumber'] = $selectedBatchNumber;
                $details['outbound_quantity'] = ($hasSalesOrderDetail["quantity"] - ($hasSalesOrderDetail["delivery_number"] - $selectedNumber));
            }
            $goodsDetails[] = $details;
        }
        return CatchResponse::success([
            'sales_order_id' => $data['id'],
            'customer_info_id' => $data['customer_info_id'],
            'company_id' => $data['company_id'],
            'goodsDetails' => $goodsDetails
        ]);
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
//        if (isset($params['warehouse_id']) && !empty($params['warehouse_id'])) {
//            $queryModel = $this->salesOrderModel->where("warehouse_id", $params['supplier_id']);
//        }
        $data = $queryModel->where("status", 0)
            ->with([
                "hasSupplierLicense" => function ($query) {
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