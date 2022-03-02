<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/1/27
 * Time: 16:39
 */

namespace catchAdmin\purchase\controller;


use app\Request;
use catchAdmin\basisinfo\model\SupplierLicense;
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
     * @author 1131191695@qq.com
     */
    public function index()
    {
        $status = [
            "未完成", "已完成", "作废"
        ];
        $data = $this->purchaseOrderModel->getList();
        foreach ($data as &$datum) {
            $datum['status_i'] = $status[$datum['status']];
//            $datum['detail'] = $status[$datum['status']];
            $datum['settlement_type_i'] = $datum['settlement_type'] == 0 ? "现结" : "月结";
        }
        return CatchResponse::paginate($data);
    }

    /**
     * 保存
     *
     * @param Request $request
     * @return \think\response\Json
     * @author 1131191695@qq.com
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
     * @author 1131191695@qq.com
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
     * @author 1131191695@qq.com
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

    /**
     * 获取采购订单的详情
     *
     * @return \think\response\Json
     * @throws \think\db\exception\DbException
     * @author 1131191695@qq.com
     */
    public function getPurchaseOrderDetails(request $request)
    {
        $purchaseOrderId = $request->param("purchaser_order_id");
        if (!$purchaseOrderId) {
            return CatchResponse::fail("请选择采购订单");
        }
        $data = $this->purchaseOrderDetailsModel->with(
            [
                "hasProductData", "hasProductSkuData"
            ]
        )->field([
            "quantity", "note", "id", "product_id", "product_sku_id", "warehousing_quantity"
        ])->whereRaw("(quantity - warehousing_quantity - return_quantity) > 0")
            ->where("purchase_order_id", $purchaseOrderId)->select()->toArray();
        $skuMap = [];
        foreach ($data as $datum) {
            $skuMap[] = [
                'id' => $datum['id'],
                'product_sku_id' => $datum['hasProductSkuData']['id'],
                'product_id' => $datum['hasProductSkuData']['product_id'],
                'product_code' => $datum['hasProductSkuData']['product_code'],
                'sku_code' => $datum['hasProductSkuData']['sku_code'],
                'item_number' => $datum['hasProductSkuData']['item_number'],
                'unit_price' => $datum['hasProductSkuData']['unit_price'],
                'tax_rate' => $datum['hasProductSkuData']['tax_rate'],
                'n_tax_price' => $datum['hasProductSkuData']['n_tax_price'],
                'packing_size' => $datum['hasProductSkuData']['packing_size'],
                'packing_specification' => $datum['hasProductSkuData']['packing_specification'],
                'product_name' => $datum['hasProductData']['product_name'],
                'udi' => $datum['hasProductSkuData']['udi'],
                'entity' => $datum['hasProductSkuData']['entity'],
                "quantity" => $datum["quantity"],
                "warehousing_quantity" => $datum["warehousing_quantity"],
                "note" => $datum["note"],
            ];
        }
        return CatchResponse::success($skuMap);
    }


    /**
     * 获取采购订单数据
     *
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author 1131191695@qq.com
     */
    public function tableGetPurchaseOrderLists()
    {
        $data = $this->purchaseOrderDetailsModel->alias("pod")
            ->join("purchase_order po", "po.id = pod.purchase_order_id")
            ->field(["po.id", "po.purchase_code"])
            ->where("po.audit_status", 1) // 已审核
            ->where("po.status", 0) // 未完成
            ->where(function ($query) {
                $query->where([
                    "po.settlement_type" => 0,
                    "po.settlement_status" => 1,
                ])->whereOr([
                    "po.settlement_type" => 1,
                    "po.settlement_status" => 0,
                ]);
            })->whereRaw("(pod.quantity - pod.warehousing_quantity - pod.return_quantity) > 0")
            ->order("po.id", "desc")
            ->group("po.id")
            ->select()->toArray();
        $map = [];
        foreach ($data as $datum) {
            $map[] = [
                "label" => $datum['purchase_code'],
                "value" => (string)$datum['id'],
            ];
        }
        return $map;
    }
}