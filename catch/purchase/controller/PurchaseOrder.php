<?php
/**
 * Created by PhpStorm.
 * author: xiejiaqing
 * Note: Tired as a dog
 * Date: 2022/1/27
 * Time: 16:39
 */

namespace catchAdmin\purchase\controller;


use app\Request;
use catchAdmin\purchase\model\PurchaseOrderDetails;
use catchAdmin\purchase\request\PurchaseOrderRequest;
use catcher\base\CatchController;
use catchAdmin\purchase\model\PurchaseOrder as PurchaseOrderModel;
use catcher\CatchResponse;

/**
 * 采购订单
 * Class PurchaseOrder
 * @package catchAdmin\purchase\controller
 */
class PurchaseOrder extends CatchController
{
    protected $purchaseOrderModel;
    protected $purchaseOrderDetailsModel;

    public function __construct(
        PurchaseOrderModel $purchaseOrderModel,
        PurchaseOrderDetails $purchaseOrderDetailsModel
    )
    {
        $this->purchaseOrderModel = $purchaseOrderModel;
        $this->purchaseOrderDetailsModel = $purchaseOrderDetailsModel;
    }

    // 列表
    public function index()
    {
        return CatchResponse::paginate($this->purchaseOrderModel->getList());
    }

    /**
     * 保存
     *
     * @author xiejiaqing
     * @param Request $request
     * @return \think\response\Json
     */
    public function save(Request $request)
    {
        // 保存基础信息
        $params = $request->param();
        $this->validator(PurchaseOrderRequest::class, $params);
        // 保存商品
        $goodsDetails = $params['goods_details'];
        unset($params['goods_details']);
        $params['purchase_code'] = getCode("PO");
        $this->purchaseOrderModel->startTrans();
        try {
            $purchaseMap = [];
            $id = $this->purchaseOrderModel->createBy($purchaseMap);
            if (empty($id)) {
                throw new \Exception("采购订单添加失败");
            }
            foreach ($goodsDetails as &$goodsDetail) {
                $goodsDetail['purchase_order_id'] = $id;
            }
            $gId = $this->purchaseOrderDetailsModel->insertAll($goodsDetails);
            if (empty($gId)) {
                throw new \Exception("采购订单商品添加失败");
            }
            // 提交事务
            $this->purchaseOrderModel->commit();
        } catch (\Exception $exception) {
            // 回滚事务
            $this->purchaseOrderModel->rollback();
            return CatchResponse::fail($exception->getMessage());
        }
        return CatchResponse::success();
    }

    /**
     * 更新
     *
     * @author xiejiaqing
     * @param Request $request
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function update(Request $request)
    {
        // 保存基础信息
        $params = $request->param();
        $this->validator(PurchaseOrderRequest::class, $params);
        if (empty($params['id'])) {
            return CatchResponse::fail("更新缺失主键id");
        }
        // 保存商品
        $goodsDetails = $params['goods_details'];
        unset($params['goods_details']);

        $this->purchaseOrderModel->startTrans();
        // 添加事务 排他锁
        $purchaseOrderModel = $this->purchaseOrderModel->where("id", $params['id'])->lock(true)->find();
        if (empty($purchaseOrderModel)) {
            return CatchResponse::fail("不存在当前数据");
        }
        // 审核成功不可以修改
        if ($purchaseOrderModel['audit_status'] == 1) {
            return CatchResponse::fail("采购订单已审核,无法修改");
        }
        if ($purchaseOrderModel['status'] == 1) {
            return CatchResponse::fail("采购订单已完成,无法修改");
        }
        try {

            $b = $this->purchaseOrderDetailsModel->where("product_id", $params['id'])->delete();
            if (!$b) {
                throw new \Exception("清除采购订单商品失败");
            }
            $pb = $this->purchaseOrderModel->updateBy($params['id'], $params);
            if (!$pb) {
                throw new \Exception("修改采购订单失败");
            }
            // 重新添加商品数据
            $map = [];
            foreach ($goodsDetails as $goodsDetail) {
                $map[] = [
                    'purchase_order_id' => $params['id'],
                    'product_id' => $goodsDetail['id'],
                    'price' => $params['id'],
                    'tax_price' => $params['id'],
                    'quantity' => $goodsDetail['number'],
                    'receipt_quantity' => 0,
                    'warehousing_quantity' => 0,
                    'return_quantity' => 0,
                    'note' => $goodsDetail['note'],
                ];
            }
            $gId = $this->purchaseOrderDetailsModel->insertAll($map);
            if (empty($gId)) {
                throw new \Exception("采购订单商品添加失败");
            }
            // 清除采购订单的商品数据
            $this->purchaseOrderModel->commit();
        } catch (\Exception $exception) {
            $this->purchaseOrderModel->rollback();
            return CatchResponse::fail($exception->getMessage());
        }
        return CatchResponse::success();
    }

    /**
     * 审核
     *
     * @author xiejiaqing
     * @param Request $request
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function audit(Request $request)
    {
        // 保存基础信息
        $params = $request->param();
        $this->validator(PurchaseOrderRequest::class, $params);
        if (empty($params['id'])) {
            return CatchResponse::fail("更新缺失主键id");
        }
        $this->purchaseOrderModel->startTrans();
        // 添加事务 排他锁
        $purchaseOrderModel = $this->purchaseOrderModel->where("id", $params['id'])->lock(true)->find();
        if (empty($purchaseOrderModel)) {
            return CatchResponse::fail("不存在当前数据");
        }
        // 审核成功不可以修改
        if ($purchaseOrderModel['audit_status'] == 1) {
            return CatchResponse::fail("采购订单已审核,无法修改");
        }
        if ($purchaseOrderModel['status'] == 1) {
            return CatchResponse::fail("采购订单已完成,无法修改");
        }
        // 更新
        try {
            $b = $this->purchaseOrderDetailsModel->updateBy($params['id'], [
                'audit_status' => $params['audit_status'],
                'audit_info' => $params['audit_info'],
                'audit_user_id' => request()->user()->id,
                'audit_user_name' => request()->user()->username,
            ]);
            if (!$b) {
                throw new \Exception("清除采购订单商品失败");
            }
            $this->purchaseOrderModel->commit();
        } catch (\Exception $exception) {
            $this->purchaseOrderModel->rollback();
            return CatchResponse::fail($exception->getMessage());
        }
        return CatchResponse::success();
    }
}