<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/3/27
 * Time: 21:38
 */

namespace catchAdmin\inventory\controller;


use app\Request;
use catchAdmin\inventory\model\Inventory;
use catchAdmin\inventory\model\InventoryBatch;
use catchAdmin\inventory\model\ReadyOutboundDetails;
use catchAdmin\inventory\model\TurnSalesRecord;
use catchAdmin\salesManage\controller\SalesOrder;
use catcher\base\CatchController;
use catchAdmin\inventory\model\ReadyOutbound as ReadyOutboundModel;
use catcher\base\CatchModel;
use catcher\CatchResponse;
use catcher\exceptions\BusinessException;
use Exception;
use fire\data\ChangeStatus;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\Model;
use think\response\Json;

/**
 * 备货出库
 *
 * Class ReadyOutbound
 * @package catchAdmin\inventory\controller
 */
class ReadyOutbound extends CatchController
{
    protected $readyOutboundModel;
    protected $readyOutboundDetails;
    // 库存
    protected $inventory;
    protected $inventoryBatch;


    public function __construct(
        ReadyOutboundModel   $readyOutboundModel,
        ReadyOutboundDetails $readyOutboundDetails,
        Inventory            $inventory,
        InventoryBatch       $inventoryBatch
    )
    {
        $this->readyOutboundModel = $readyOutboundModel;
        $this->readyOutboundDetails = $readyOutboundDetails;
        $this->inventory = $inventory;
        $this->inventoryBatch = $inventoryBatch;
    }

    public function index()
    {
        $data = $this->readyOutboundModel->getList();
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
        $data = $request->param();
        $this->readyOutboundModel->startTrans();
        try {
            $goodsDetails = $data['goods'];
            unset($data['goods']);
            // 检查是否存在出库商品
            $data['outbound_time'] = strtotime($data['outbound_time']);
            $data['company_id'] = request()->user()->department_id;
            if (isset($data['id']) && !empty($data['id'])) {
                // 存在数据
                $consignmentOutboundData = $this->clearOldData($data['id']);
                if ($consignmentOutboundData['audit_status'] != 0) {
                    throw new BusinessException("订单已审核，无法修改");
                }
                if ($consignmentOutboundData['status'] != 0) {
                    throw new BusinessException("订单状态不为未完成，无法修改");
                }
                $id = $data['id'];
                $consignmentOutboundData->updateBy($id, $data);
            } else {
                $data['ready_outbound_code'] = getCode('RO');
                $id = $this->readyOutboundModel->insertGetId($data);
            }

            $details = [];
            $totalNumber = 0;
            $totalAmount = 0;
            foreach ($goodsDetails as $goods_detail) {
                if (empty($goods_detail['put_num'])) {
                    continue;
                }
                $batchData = $this->inventoryBatch->where("id", $goods_detail['id'])->find();
                if (empty($batchData)) {
                    throw new BusinessException("存在库存批次无效");
                }
                if (($batchData['number'] - $batchData['use_number']) < $goods_detail['put_num']) {
                    throw new BusinessException("存在批次库存不足");
                }
                // 组装数据
                $details[] = [
                    'ready_outbound_id' => $id,
                    'inventory_id' => $goods_detail['inventory_id'],
                    'inventory_batch_id' => $goods_detail['id'],
                    'product_id' => $goods_detail['product_id'],
                    'product_sku_id' => $goods_detail['product_sku_id'],
                    'product_code' => $goods_detail['product_code'],
                    'item_number' => $goods_detail['item_number'],
                    'sku_code' => $goods_detail['product_sku_name'],
                    'tax_rate' => $goods_detail['tax_rate'],
                    'unit_price' => $goods_detail['unit_price'],
                    'amount' => bcmul($goods_detail['unit_price'], $goods_detail['put_num'], 2),
                    'quantity' => $goods_detail['put_num'],
                ];
                $totalNumber += $goods_detail['put_num'];
                $totalAmount = bcadd($totalAmount, bcmul($goods_detail['put_num'], $goods_detail['unit_price'], 2), 2);
                $this->inventoryBatch->where("id", $goods_detail['id'])->increment("use_number", $goods_detail['put_num']);
                $this->inventory->where("id", $goods_detail['inventory_id'])->increment("use_number", $goods_detail['put_num']);
            }
            if (empty($details)) {
                throw new BusinessException("商品数据为空");
            }
            $this->readyOutboundModel->updateBy($id, [
                'put_num' => $totalNumber,
                'amount' => $totalAmount,
            ]);
            $this->readyOutboundDetails->insertAll($details);
            $this->readyOutboundModel->commit();
            return CatchResponse::success(['id' => $id]);
        } catch (Exception $exception) {
            $this->readyOutboundModel->rollback();
            throw new BusinessException($exception->getMessage());
        }
    }

    public function update(Request $request)
    {
        return $this->save($request);
    }

    /**
     * @param $id
     * @return array|CatchModel|mixed|Model|null
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author 1131191695@qq.com
     */
    private function clearOldData($id, $isClear = true)
    {
        $consignmentOutboundData = $this->readyOutboundModel->getFindByKey($id);
        $details = $this->readyOutboundDetails->where('ready_outbound_id', $id)->select();
        foreach ($details as $detail) {
            // 恢复库存数据
            $this->inventoryBatch->where("id", $detail['inventory_batch_id'])->decrement("use_number", $detail['quantity']);;
            $this->inventory->where("id", $detail['inventory_id'])->decrement("use_number", $detail['quantity']);;
        }
        // 清除旧数据
        if ($isClear) {
            $this->readyOutboundDetails->destroy(['ready_outbound_id' => $id]);
        }
        return $consignmentOutboundData;
    }

    /**
     * 作废
     *
     * @param Request $request
     * @return Json
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author 1131191695@qq.com
     */
    public function invalid(Request $request)
    {
        $data = $request->param();
        $this->readyOutboundModel->startTrans();
        try {
            $consignmentOutboundData = $this->clearOldData($data['id'], false);
            if ($consignmentOutboundData['audit_status'] != 0) {
                throw new BusinessException("订单已审核，无法作废");
            }
            if ($consignmentOutboundData['status'] != 0) {
                throw new BusinessException("订单状态不为未完成，无法作废");
            }
            $this->readyOutboundModel->updateBy($data['id'], [
                'status' => 2
            ]);
            $this->readyOutboundModel->commit();
        } catch (Exception $exception) {
            $this->readyOutboundModel->rollback();
            throw new BusinessException($exception->getMessage());
        }
        return CatchResponse::success();
    }

    /**
     * 审核
     *
     * @param Request $request
     * @return Json
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author 1131191695@qq.com
     */
    public function audit(Request $request)
    {
        // 保存基础信息
        $params = $request->param();
        if (empty($params['id'])) {
            return CatchResponse::fail("更新缺失主键id");
        }
        $this->readyOutboundModel->startTrans();
        // 添加事务 排他锁
        $purchaseOrderModel = $this->readyOutboundModel->getFindByKey($params['id']);
        if (empty($purchaseOrderModel)) {
            return CatchResponse::fail("不存在当前数据");
        }
        // 审核成功不可以修改
        if ($purchaseOrderModel['audit_status'] == 1) {
            return CatchResponse::fail("订单已审核,无法修改");
        }
        if ($purchaseOrderModel['status'] == 1) {
            return CatchResponse::fail("订单已完成,无法修改");
        }
        // 更新
        try {
            $b = $this->readyOutboundModel->updateBy($params['id'], [
                'audit_status' => $params['audit_status'],
                'audit_info' => $params['audit_info'],
                'audit_user_id' => request()->user()->id,
                'audit_user_name' => request()->user()->username,
            ]);
            if (!$b) {
                throw new \Exception("审核订单失败");
            }
            $this->readyOutboundModel->commit();
        } catch (\Exception $exception) {
            $this->readyOutboundModel->rollback();
            return CatchResponse::fail($exception->getMessage());
        }
        return CatchResponse::success();
    }

    /**
     * 转销售
     *
     * @param Request $request
     * @return Json
     * @author 1131191695@qq.com
     */
    public function turnSales(Request $request)
    {
        // 转销售
        $params = $request->param();
        if (empty($params)) {
            throw new BusinessException("数据为空");
        }
        $this->readyOutboundModel->startTrans();
        try {
            $readyData = $this->readyOutboundModel->getFindByKey($params['id']);
            if (empty($readyData)) {
                throw new BusinessException("不存在当前订单");
            }
            $salesOrderMap = [
                "sales_time" => date("Y-m-d"),
                "salesman_id" => $params['salesman_id'],
                "supplier_id" => $params['supplier_id'],
                "customer_info_id" => $params['customer_info_id'],
                "sales_type" => "3",
                "settlement_status" => 0,
                "id" => 0,
            ];
            $goodsMap = [];
            $inventoryQuantity = 0;
            $turnSalesData = [];
            foreach ($params['goods'] as $good) {
                if (!isset($good['inventory_quantity_t']) && empty($good['inventory_quantity_t'])) {
                    continue;
                }
                $cData = $this->readyOutboundDetails->where("id", $good['details_id'])->find();
                if (($cData['quantity'] - $cData['inventory_quantity'] - $cData['resold_quantity']) < $good['inventory_quantity_t']) {
                    throw new BusinessException("存在商品不够数量转销售");
                }
                if (!isset($goodsMap[$good['product_sku_id']])) {
                    $goodsMap[$good['product_sku_id']] = [
                        "id" => $good['product_sku_id'],
                        "product_id" => $good['product_id'],
                        "product_code" => $good['product_code'],
                        "sku_code" => $good['sku_code'],
                        "item_number" => $good['item_number'],
                        "unit_price" => $good['unit_price'],
                        "tax_rate" => $good['tax_rate'],
                        "product_name" => $good['product_name'],
                        "quantity" => $good['inventory_quantity_t'],
                        "note" => "",
                        "total_price" => $good['unit_price'],
                    ];
                } else {
                    $goodsMap[$good['product_sku_id']]['quantity'] += $good['inventory_quantity_t'];
                }
                $turnSalesData[] = [
                    'form_id' => $params['id'],
                    'form_details_id' => $good['details_id'],
                    'inventory_id' => $good['inventory_id'],
                    'inventory_batch_id' => $good['inventory_batch_id'],
                    'quantity' => $good['inventory_quantity_t'],
                    'form_type' => 2
                ];
                $inventoryQuantity = bcadd($inventoryQuantity, $good['inventory_quantity_t']);
                $this->readyOutboundDetails->where("id", $good['details_id'])->increment("inventory_quantity", $good['inventory_quantity_t']);
            }
            if (empty($goodsMap) || $inventoryQuantity == 0) {
                throw new BusinessException("没有出库的数据");
            }
            $this->readyOutboundModel->where("id", $params['id'])->increment('inventory_quantity', $inventoryQuantity);
            $salesOrderMap['goods_details'] = array_values($goodsMap);
            // 添加销售订单
            $salesOrderData = app(SalesOrder::class)->insert($salesOrderMap);
            if (isset($salesOrderData['data']['id']) && !empty($salesOrderData['data']['id'])) {
                foreach ($turnSalesData as &$turnSalesDatum) {
                    $turnSalesDatum['sales_order_id'] = $salesOrderData['data']['id'];
                }
                app(TurnSalesRecord::class)->insertAll($turnSalesData);
            } else {
                throw new BusinessException("保存销售订单失败");
            }
            $this->readyOutboundModel->commit();
        } catch (Exception $exception) {
            $this->readyOutboundModel->rollback();
            throw new BusinessException($exception->getMessage());
        }
        return CatchResponse::success();
    }

    /**
     * 入库
     *
     * @param Request $request
     * @return Json
     * @author 1131191695@qq.com
     */
    public function stockIn(Request $request)
    {
        $params = $request->param();
        if (empty($params)) {
            throw new BusinessException("数据为空");
        }
        $this->readyOutboundModel->startTrans();
        try {
            $readyData = $this->readyOutboundModel->getFindByKey($params['id']);
            if (empty($readyData)) {
                throw new BusinessException("不存在当前订单");
            }
            $resold_quantity = 0;
            foreach ($params['goods'] as $good) {
                if (!isset($good['resold_quantity_t']) && empty($good['resold_quantity_t'])) {
                    continue;
                }
                $cData = $this->readyOutboundDetails->where("id", $good['details_id'])->find();
                if (($cData['quantity'] - $cData['inventory_quantity'] - $cData['resold_quantity']) < $good['resold_quantity_t']) {
                    throw new BusinessException("存在商品不够数量转销售");
                }
                $resold_quantity = bcadd($resold_quantity, $good['resold_quantity_t']);
                $this->readyOutboundDetails->where("id", $good['details_id'])->increment('resold_quantity', $good['resold_quantity_t']);
                // 减少使用库存
                $this->inventory->where('id', $good['inventory_id'])->decrement('use_number', $good['resold_quantity_t']);
                $this->inventoryBatch->where('id', $good['inventory_batch_id'])->decrement('use_number', $good['resold_quantity_t']);
            }
            if ($resold_quantity == 0) {
                throw new BusinessException("入库数为空");
            }
            $this->readyOutboundModel->where("id", $params['id'])->increment('resold_quantity', $resold_quantity);
            $this->readyOutboundModel->commit();
        } catch (Exception $exception) {
            $this->readyOutboundModel->rollback();
            throw new BusinessException($exception->getMessage());
        }
        return CatchResponse::success();
    }
}