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
use catchAdmin\inventory\model\OtherOutboundDetails;
use catcher\base\CatchController;
use catchAdmin\inventory\model\OtherOutbound as OtherOutboundModel;
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
 * 其他出库
 *
 * Class OtherOutbound
 * @package catchAdmin\inventory\controller
 */
class OtherOutbound extends CatchController
{
    protected $otherOutboundModel;
    protected $otherOutboundDetails;
    // 库存
    protected $inventory;
    protected $inventoryBatch;

    public function __construct(
        OtherOutboundModel   $otherOutboundModel,
        OtherOutboundDetails $otherOutboundDetails,
        Inventory            $inventory,
        InventoryBatch       $inventoryBatch
    )
    {
        $this->otherOutboundModel = $otherOutboundModel;
        $this->otherOutboundDetails = $otherOutboundDetails;
        $this->inventory = $inventory;
        $this->inventoryBatch = $inventoryBatch;
    }

    /**
     * 列表
     *
     * @return Json
     * @throws DbException
     * @author 1131191695@qq.com
     */
    public function index()
    {
        $data = $this->otherOutboundModel->getList();
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
        $this->otherOutboundModel->startTrans();
        try {
            $goodsDetails = $data['goods'];
            unset($data['goods']);
            // 检查是否存在出库商品
            $data['outbound_time'] = strtotime($data['outbound_time']);
            $data['company_id'] = request()->user()->department_id;
            if (isset($data['id']) && !empty($data['id'])) {
                // 存在数据
                $otherPutInventoryModel = $this->clearOldData($data['id']);
                if ($otherPutInventoryModel['audit_status'] != 0) {
                    throw new BusinessException("订单已审核，无法修改");
                }
                if ($otherPutInventoryModel['status'] != 0) {
                    throw new BusinessException("订单状态不为未完成，无法修改");
                }
                $id = $data['id'];
                $otherPutInventoryModel->updateBy($id, $data);
            } else {
                $data['other_outbound_code'] = getCode('CO');
                $id = $this->otherOutboundModel->insertGetId($data);
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
                    'other_outbound_id' => $id,
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
            $this->otherOutboundModel->updateBy($id, [
                'put_num' => $totalNumber,
                'amount' => $totalAmount,
            ]);
            $this->otherOutboundDetails->insertAll($details);
            $this->otherOutboundModel->commit();
            return CatchResponse::success(['id' => $id]);
        } catch (Exception $exception) {
            $this->otherOutboundModel->rollback();
            throw new BusinessException($exception->getMessage());
        }
    }

    /**
     * 更新
     *
     * @param Request $request
     * @return Json
     * @author 1131191695@qq.com
     */
    public function update(Request $request)
    {
        return $this->save($request);
    }

    /**
     * 清除
     *
     * @param $id
     * @param bool $isClear
     * @return array|CatchModel|mixed|Model|null
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author 1131191695@qq.com
     */
    private function clearOldData($id, $isClear = true)
    {
        $model = $this->otherOutboundModel->getFindByKey($id);
        if (empty($model)) {
            throw new BusinessException("不存在当前数据");
        }
        // 审核成功不可以修改
        if ($model['audit_status'] == 1) {
            throw new BusinessException("订单已审核,无法修改");
        }
        if ($model['status'] == 1) {
            throw new BusinessException("订单已完成,无法修改");
        }
        if ($model['status'] == 2) {
            throw new BusinessException("订单已作废,无法修改");
        }
        $details = $this->otherOutboundDetails->where('other_outbound_id', $id)->select();
        foreach ($details as $detail) {
            // 恢复库存数据
            $this->inventoryBatch->where("id", $detail['inventory_batch_id'])->decrement("use_number", $detail['quantity']);;
            $this->inventory->where("id", $detail['inventory_id'])->decrement("use_number", $detail['quantity']);;
        }
        // 清除旧数据
        if ($isClear) {
            $this->otherOutboundDetails->destroy(['other_outbound_id' => $id]);
        }
        return $model;
    }

    /**
     * 审核
     *
     * @param Request $request
     * @return Json
     * @author 1131191695@qq.com
     */
    public function audit(Request $request)
    {
        // 保存基础信息
        $params = $request->param();
        if (empty($params['id'])) {
            return CatchResponse::fail("更新缺失主键id");
        }
        $this->otherOutboundModel->startTrans();
        try {
            $purchaseOrderModel = $this->otherOutboundModel->getFindByKey($params['id']);
            if (empty($purchaseOrderModel)) {
                throw new BusinessException("不存在当前数据");
            }
            // 审核成功不可以修改
            if ($purchaseOrderModel['audit_status'] == 1) {
                throw new BusinessException("订单已审核,无法修改");
            }
            if ($purchaseOrderModel['status'] == 1) {
                throw new BusinessException("订单已完成,无法修改");
            }
            $b = $this->otherOutboundModel->updateBy($params['id'], [
                'audit_status' => $params['audit_status'],
                'audit_info' => $params['audit_info'],
                'audit_user_id' => request()->user()->id,
                'audit_user_name' => request()->user()->username,
            ]);
            if (!$b) {
                throw new \Exception("审核失败");
            }
            $this->otherOutboundModel->commit();
        } catch (\Exception $exception) {
            $this->otherOutboundModel->rollback();
            return CatchResponse::fail($exception->getMessage());
        }
        return CatchResponse::success();
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
        // 保存基础信息
        $params = $request->param();
        if (empty($params['id'])) {
            return CatchResponse::fail("更新缺失主键id");
        }
        $this->otherOutboundModel->startTrans();
        // 更新
        try {
            // 添加事务 排他锁
            $model = $this->clearOldData($params['id'], false);
            $this->otherOutboundModel->updateBy($params['id'], [
                'status' => 2
            ]);
            $this->otherOutboundModel->commit();
        } catch (\Exception $exception) {
            $this->otherOutboundModel->rollback();
            throw new BusinessException($exception->getMessage());
        }
        return CatchResponse::success();
    }
}