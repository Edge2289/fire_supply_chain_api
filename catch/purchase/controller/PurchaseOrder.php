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
        PurchaseOrderModel   $purchaseOrderModel,
        PurchaseOrderDetails $purchaseOrderDetailsModel
    )
    {
        $this->purchaseOrderModel = $purchaseOrderModel;
        $this->purchaseOrderDetailsModel = $purchaseOrderDetailsModel;
    }

    /**
     * 列表
     *
     * @return \think\response\Json
     * @author xiejiaqing
     */
    public function index()
    {
        return CatchResponse::paginate($this->purchaseOrderModel->getList());
    }

    /**
     * 保存
     *
     * @param Request $request
     * @return \think\response\Json
     * @author xiejiaqing
     */
    public function save(Request $request)
    {
        // 保存基础信息
        $params = $request->param();
        $this->validator(PurchaseOrderRequest::class, $params);
        // 保存商品
        $goodsDetails = $params['goods_details'];
        unset($params['goods_details'], $params['id']);
        $params['purchase_code'] = getCode("PO");
        $this->purchaseOrderModel->startTrans();
        try {
            $params['purchase_date'] = strtotime($params['purchase_date']);
            $id = $this->purchaseOrderModel->createBy($params);
            if (empty($id)) {
                throw new \Exception("采购订单添加失败");
            }
            $totalNum = 0;
            $totalPrice = 0;
            // 重新添加商品数据
            foreach ($goodsDetails as $goodsDetail) {
                $totalNum += $goodsDetail['quantity'];
                $totalPrice = bcadd($totalPrice, bcmul($goodsDetail['unit_price'], $goodsDetail['quantity'], 2), 2);
                $map[] = [
                    'purchase_order_id' => $id,
                    'product_id' => $goodsDetail['product_id'] ?? 0,
                    'product_sku_id' => $goodsDetail['id'],
                    'product_code' => $goodsDetail['product_code'],
                    'item_number' => $goodsDetail['item_number'],
                    'sku_code' => $goodsDetail['sku_code'],
                    'unit_price' => $goodsDetail['unit_price'],
                    'tax_rate' => $goodsDetail['tax_rate'],
                    'quantity' => $goodsDetail['quantity'],
                    'receipt_quantity' => 0,
                    'warehousing_quantity' => 0,
                    'return_quantity' => 0,
                    'note' => $goodsDetail['note'] ?? "",
                ];
            }
            $gId = $this->purchaseOrderDetailsModel->insertAll($map);
            if (empty($gId)) {
                throw new \Exception("采购订单商品添加失败");
            }
            $this->purchaseOrderModel->updateBy($id, [
                'num' => $totalNum,
                'amount' => (string)$totalPrice,
            ]);
            // 提交事务
            $this->purchaseOrderModel->commit();
        } catch (\Exception $exception) {
            // 回滚事务
            $this->purchaseOrderModel->rollback();
            return CatchResponse::fail($exception->getMessage());
        }
        return CatchResponse::success(['id' => $id]);
    }

    /**
     * 更新
     *
     * @param Request $request
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author xiejiaqing
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
            $params['purchase_date'] = strtotime($params['purchase_date']);
            $b = $this->purchaseOrderDetailsModel->destroy([
                'purchase_order_id' => 1
            ]);
            if (!$b) {
                throw new \Exception("清除采购订单商品失败");
            }
            $pb = $this->purchaseOrderModel->updateBy($params['id'], $params);
            if (!$pb) {
                throw new \Exception("修改采购订单失败");
            }
            $totalNum = 0;
            $totalPrice = 0;
            // 重新添加商品数据
            $map = [];
            foreach ($goodsDetails as $goodsDetail) {
                $totalNum += $goodsDetail['quantity'];
                $totalPrice = bcadd($totalPrice, bcmul($goodsDetail['unit_price'], $goodsDetail['quantity'], 2), 2);;
                $map[] = [
                    'purchase_order_id' => $params['id'],
                    'product_id' => $goodsDetail['product_id'] ?? 0,
                    'product_sku_id' => $goodsDetails['id'],
                    'product_code' => $goodsDetail['product_code'],
                    'item_number' => $goodsDetail['item_number'],
                    'sku_code' => $goodsDetail['sku_code'],
                    'unit_price' => $goodsDetail['unit_price'],
                    'tax_rate' => $goodsDetail['tax_rate'],
                    'quantity' => $goodsDetail['quantity'],
                    'receipt_quantity' => 0,
                    'warehousing_quantity' => 0,
                    'return_quantity' => 0,
                    'note' => $goodsDetail['note'] ?? "",
                ];
            }
            $gId = $this->purchaseOrderDetailsModel->insertAll($map);
            if (empty($gId)) {
                throw new \Exception("采购订单商品添加失败");
            }
            $this->purchaseOrderModel->updateBy($params['id'], [
                'num' => $totalNum,
                'amount' => (string)$totalPrice,
            ]);
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
     * @param Request $request
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author xiejiaqing
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
            $b = $this->purchaseOrderModel->updateBy($params['id'], [
                'audit_status' => $params['audit_status'],
                'audit_info' => $params['audit_info'],
                'audit_user_id' => request()->user()->id,
                'audit_user_name' => request()->user()->username,
            ]);
            if (!$b) {
                throw new \Exception("审核采购订单失败");
            }
            $this->purchaseOrderModel->commit();
        } catch (\Exception $exception) {
            $this->purchaseOrderModel->rollback();
            return CatchResponse::fail($exception->getMessage());
        }
        return CatchResponse::success();
    }

    // 结单/返回结单
    public function statement()
    {

    }

    // 作废
    public function invalid()
    {

    }

    /**
     * 获取弹窗选择回款单的采购订单
     *
     * @param Request $request
     * @return \think\response\Json
     * @throws \think\db\exception\DbException
     * @author xiejiaqing
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
        $queryModel = $this->purchaseOrderModel;
        if (isset($params['supplier_id']) && !empty($params['supplier_id'])) {
            $queryModel = $this->purchaseOrderModel->where("supplier_id", $params['supplier_id']);
        }
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
        $supple = $this->purchaseOrderModel->getSupplierLicense();
        return CatchResponse::success([
            "data" => $data,
            "supple" => $supple
        ]);
    }
}