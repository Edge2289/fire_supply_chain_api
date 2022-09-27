<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/3/14
 * Time: 20:47
 */

namespace catchAdmin\salesManage\controller;


use catchAdmin\basisinfo\model\SupplierLicense;
use catchAdmin\inventory\model\Inventory;
use catchAdmin\inventory\model\InventoryBatch;
use catchAdmin\inventory\model\Warehouse;
use catchAdmin\salesManage\model\OutboundOrderDetails;
use catchAdmin\salesManage\model\SalesOrderDetailsModel;
use catchAdmin\salesManage\model\SalesOrderModel;
use catcher\base\CatchController;
use catchAdmin\salesManage\model\OutboundOrder as OutboundOrderModel;
use catcher\CatchResponse;
use catcher\exceptions\BusinessException;
use fire\data\ChangeStatus;
use think\db\exception\DbException;
use think\facade\Cache;
use think\Request;
use think\response\Json;

/**
 * Class OutboundOrder
 * @package catchAdmin\salesManage\controller
 */
class OutboundOrder extends CatchController
{
    // 出库单
    private $outboundOrderModel;
    private $outboundOrderDetails;
    private $salesOrderModel; // 销售订单
    private $salesOrderDetailsModel; // 销售订单商品详情

    // 仓库
    private $warehouse;
    // 库存
    private $inventory;
    private $inventoryBatch;

    public function __construct(
        OutboundOrderModel     $outboundOrder,
        OutboundOrderDetails   $outboundOrderDetails,
        Warehouse              $warehouse,
        Inventory              $inventory,
        InventoryBatch         $inventoryBatch,
        SalesOrderModel        $salesOrderModel,
        SalesOrderDetailsModel $salesOrderDetailsModel
    )
    {
        $this->outboundOrderModel = $outboundOrder;
        $this->outboundOrderDetails = $outboundOrderDetails;
        $this->warehouse = $warehouse;
        $this->inventory = $inventory;
        $this->inventoryBatch = $inventoryBatch;
        $this->salesOrderModel = $salesOrderModel;
        $this->salesOrderDetailsModel = $salesOrderDetailsModel;
    }

    /**
     * 获取列表
     *
     * @return Json
     * @throws DbException
     * @author 1131191695@qq.com
     */
    public function index()
    {
        $data = $this->outboundOrderModel->getList();
        ChangeStatus::getInstance()->audit()->status()->handle($data);
        return CatchResponse::paginate($data);
    }

    /**
     * 保存
     *
     * @param Request $request
     * @return Json
     * @author 1131191695@qq.com
     */
    public function save(Request $request)
    {
        // 保存基础信息
        $params = $request->param();
        if (!is_concurrent(self::class)) {
            return CatchResponse::fail("请重新操作");
        }
        Cache::set(self::class, 1);
        $this->salesOrderModel->startTrans();
        try {
            $params['outbound_time'] = strtotime($params['outbound_time']);
            if (isset($params['id']) && !empty($params['id'])) {
                $this->restoreOutBoundOrder($params['id']);
            }
            $salesOrderData = $this->salesOrderModel->getFindByKey($params['sales_order_id']);
            if ($salesOrderData['status'] != 0) {
                throw new BusinessException("订单不是未完成，无法出库");
            }
            if ($salesOrderData['audit_status'] != 1) {
                throw new BusinessException("订单未审核, 无法出库");
            }
            if ($salesOrderData['settlement_type'] == 0 && $salesOrderData['settlement_status'] == 0) {
                // 结算类型为现结，但是未结算
                throw new BusinessException("订单结算类型为现结，但未结算");
            }
            if ($salesOrderData['customer_info_id'] != $params['customer_info_id']) {
                throw new BusinessException("订单客户归属有误");
            }
            $outboundOrderMap = [
                'outbound_order_code' => getCode('DO'),
                'sales_order_id' => $params['sales_order_id'],
                'company_id' => request()->user()->department_id,
                'outbound_time' => $params['outbound_time'],
                'outbound_man_id' => request()->user()->id,
                'supplier_id' => $salesOrderData['supplier_id'],
                'customer_info_id' => $salesOrderData['customer_info_id'],
                'warehouse_id' => $params['warehouse_id'],
                'remark' => $params['remark'],
                'logistics_code' => $params['logistics_code'] ?? '',
                'logistics_number' => $params['logistics_number'] ?? '',
            ];
            $warehouseData = $this->warehouse->getFindByKey($params['warehouse_id']);
            if (!$warehouseData) {
                throw new BusinessException("仓库不存在");
            }

            if (isset($params['id']) && !empty($params['id'])) {
                // 恢复数据
                $outboundOrderId = $params['id'];
                unset($outboundOrderMap['outbound_order_code']);
                $this->outboundOrderModel->updateBy($outboundOrderId, $outboundOrderMap);
            } else {
                $outboundOrderId = $this->outboundOrderModel->insertGetId($outboundOrderMap);
            }

            $totalNum = 0;
            $totalAmount = 0;
            $outboundOrderDetails = [];

            $changeSalesOrderDeliveryNumber = [];
            foreach ($params['goods_details'] as $goods_detail) {
                if ($goods_detail['selectedNumber'] == 0) {
                    continue;
                }
                foreach ($goods_detail['selectOutboundItem'] as $value) {
                    if (empty($value)) {
                        continue;
                    }
                    $outboundOrderDetails[] = [
                        'outbound_order_id' => $outboundOrderId,
                        'sales_order_details_id' => $goods_detail['sales_order_details_id'],
                        'inventory_id' => $value['inventory_id'],
                        'inventory_batch_id' => $value['id'],
                        'product_id' => $goods_detail['product_id'],
                        'product_sku_id' => $value['product_sku_id'],
                        'product_code' => $goods_detail['product_code'],
                        'item_number' => $goods_detail['item_number'],
                        'sku_code' => $goods_detail['sku_code'],
                        'tax_rate' => $goods_detail['tax_rate'],
                        'unit_price' => $goods_detail['unit_price'],
                        'amount' => bcmul($goods_detail['unit_price'], $value['out_number'], 2),
                        'quantity' => $value['out_number'],
                    ];
                    $totalNum += $value['out_number'];
                    $totalAmount = bcadd($totalAmount, bcmul($goods_detail['unit_price'], $value['out_number'], 2), 2);

                    if (isset($changeSalesOrderDeliveryNumber[$goods_detail['sales_order_details_id']])) {
                        $changeSalesOrderDeliveryNumber[$goods_detail['sales_order_details_id']] += $value['out_number'];
                    } else {
                        $changeSalesOrderDeliveryNumber[$goods_detail['sales_order_details_id']] = $value['out_number'];
                    }
                    // 扣除库存
                    $this->inventoryBatch->where("id", $value['id'])->increment('use_number', $value['out_number']);
                    $this->inventory->where("id", $value['inventory_id'])->increment('use_number', $value['out_number']);
                }
            }
            if (empty($outboundOrderDetails)) {
                throw new BusinessException("没有出库数量的满足");
            }
            if ($totalNum > ($salesOrderData['num'] - $salesOrderData['put_num'])) {
                throw new BusinessException("当前出库数大于所剩余的数量");
            }
            // 修改出库单的数据
            $this->outboundOrderModel->updateBy($outboundOrderId, [
                'outbound_num' => $totalNum,
                'amount' => $totalAmount,
            ]);
            // 订单的修改操作
            foreach ($changeSalesOrderDeliveryNumber as $saleOrderDetailsId => $outNumber) {
                $this->salesOrderDetailsModel->where("id", $saleOrderDetailsId)->increment('delivery_number', $outNumber);
            }
            $this->salesOrderModel->where("id", $params['sales_order_id'])->increment('put_num', $totalNum);
            $this->outboundOrderDetails->saveAll($outboundOrderDetails);
            $this->salesOrderModel->commit();

            Cache::set(self::class, 0);
            return CatchResponse::success(['id' => $outboundOrderId]);
        } catch (\Exception $exception) {

            Cache::set(self::class, 0);
            $this->salesOrderModel->rollback();
            return CatchResponse::fail($exception->getMessage());
        }
    }

    /**
     * 恢复出库单原始数据
     *
     * @param $id
     * @param bool $isClear
     * @throws DbException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @author 1131191695@qq.com
     */
    private function restoreOutBoundOrder($id, $isClear = true)
    {
        // 存在出库单并且不为空
        $outboundOrderData = $this->outboundOrderModel->getFindByKey($id);
        if (empty($outboundOrderData)) {
            throw new BusinessException("不存在当前出库单");
        }
        if ($outboundOrderData['status'] != 0) {
            throw new BusinessException("当前出库单状态不为未完成");
        }
        if ($outboundOrderData['audit_status'] == 1) {
            throw new BusinessException("当前出库单已审核，无法修改");
        }
        // 恢复零售订单的数据
        $this->salesOrderModel->where("id", $outboundOrderData['sales_order_id'])->decrement('put_num', $outboundOrderData['outbound_num']);
        $outboundOrderDetailsData = $this->outboundOrderDetails->where('outbound_order_id', $id)->select();
        // 软删除
        if ($isClear) {
            $this->outboundOrderDetails->destroy(['outbound_order_id' => $id]);
        }
        foreach ($outboundOrderDetailsData as $outboundOrderDetailsDatum) {
            $this->salesOrderDetailsModel->where('id', $outboundOrderDetailsDatum['sales_order_details_id'])->decrement('delivery_number', $outboundOrderDetailsDatum['quantity']);
            $this->inventoryBatch->where('id', $outboundOrderDetailsDatum['inventory_batch_id'])->decrement('use_number', $outboundOrderDetailsDatum['quantity']);
            $this->inventory->where('id', $outboundOrderDetailsDatum['inventory_id'])->decrement('use_number', $outboundOrderDetailsDatum['quantity']);
        }
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
        $outboundOrderData = $this->outboundOrderModel->findBy($data['id']);
        if (empty($outboundOrderData)) {
            throw new BusinessException("不存在出库订单");
        }
        if ($outboundOrderData['audit_status'] == 1) {
            throw new BusinessException("已审核");
        }
        $b = $this->outboundOrderModel->updateBy($data['id'], [
            'audit_status' => $data['audit_status'],
            'status' => $data['audit_status'] == 1 ? 1 : 0,
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
     * 作废返回销售订单的出货数
     *
     * @param Request $request
     * @return Json
     * @author 1131191695@qq.com
     */
    public function invalid(Request $request)
    {
        $data = $request->param();
        $this->outboundOrderModel->startTrans();
        try {
            $outboundOrderData = $this->outboundOrderModel->getFindByKey($data['id']);
            if (empty($outboundOrderData)) {
                throw new BusinessException("不存在销售订单");
            }
            if ($outboundOrderData['audit_status'] == 1) {
                throw new BusinessException("已审核，无法作废");
            }
            // 恢复数据
            $this->restoreOutBoundOrder($data['id']);
            $b = $this->outboundOrderModel->updateBy($data['id'], [
                'status' => 2, // 作废
            ]);
            if (!$b) {
                throw new BusinessException("作废失败");
            }
            $this->outboundOrderModel->commit();
        } catch (\Exception $exception) {
            $this->outboundOrderModel->rollback();
            throw new BusinessException($exception->getMessage());
        }
        return CatchResponse::success();
    }

    /**
     * @param $skuId
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author 1131191695@qq.com
     */
    public function getSelectOutboundOrder($outboundOrderId, $skuId)
    {
        $data = $this->outboundOrderDetails
            ->with([
                "hasProductData", "hasProductSkuData", "hasInventoryBatch"
            ])->where('outbound_order_id', $outboundOrderId)->where("product_sku_id", $skuId)->select()->toArray();
        $map = [];
        $selectedNumber = 0;
        $batchId = [];
        foreach ($data as $datum) {
            $iMap = $datum['hasInventoryBatch'];
            $datum['out_number'] = 0;
            $iMap["product_name"] = $datum["hasProductData"]["product_name"] ?? '';
            $iMap["product_sku_name"] = $datum["hasProductSkuData"]["sku_code"] ?? '';
            $iMap['out_number'] = $datum['quantity'];
            $map[] = $iMap;
            $selectedNumber += $datum['quantity'];
            if (!in_array($iMap['id'], $batchId)) {
                $batchId[] = $iMap['id'];
            }
        }
        return [$map, $selectedNumber, count($batchId)];
    }

    /**
     * 获取弹窗选择回款单出库订单
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
        $queryModel = $this->outboundOrderModel;
        if (isset($params['supplier_id']) && !empty($params['supplier_id'])) {
            $queryModel = $this->outboundOrderModel->where("supplier_id", $params['supplier_id']);
        }
        $data = $queryModel->where("status", 1)
            ->with([
                "hasSupplierLicense" => function ($query) {
                    $query->field(["id", "company_name"]);
                }
            ])
            ->where("audit_status", 1)
            ->paginate();
        return CatchResponse::success([
            "data" => $data,
            "supple" => app(SupplierLicense::class)->getSupplier()
        ]);
    }
}