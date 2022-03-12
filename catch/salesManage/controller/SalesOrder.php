<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/3/6
 * Time: 22:54
 */

namespace catchAdmin\salesManage\controller;


use catchAdmin\salesManage\model\SalesOrderDetailsModel;
use catchAdmin\salesManage\model\SalesOrderModel;
use catcher\base\CatchController;
use catcher\CatchResponse;
use catcher\exceptions\BusinessException;
use think\Request;

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

    public function index()
    {
        $status = [
            "未完成", "已完成", "作废"
        ];
        $data = $this->salesOrderModel->getList();
        foreach ($data as &$datum) {
            $datum['status_i'] = $status[$datum['status']];
//            $datum['detail'] = $status[$datum['status']];
            $datum['settlement_type_i'] = $datum['settlement_type'] == 0 ? "现结" : "月结";
        }
        return CatchResponse::paginate($data);
    }

    /**
     * 保存或者更新
     *
     * @param Request $request
     * @return \think\response\Json
     * @author 1131191695@qq.com
     */
    public function save(Request $request)
    {
        // 保存基础信息
        $params = $request->param();
//        $this->validator(SalesOrderRequest::class, $params);
        // 保存商品
        $goodsDetails = $params['goods_details'];
        unset($params['goods_details'], $params['id']);
        $params['order_code'] = getCode("SO");
        $this->salesOrderModel->startTrans();
        try {
            $params['sales_time'] = strtotime($params['sales_time']);
            $id = $this->salesOrderModel->createBy($params);
            if (empty($id)) {
                throw new \Exception("销售订单添加失败");
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
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
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

    // 作废
    public function invalid()
    {

    }
}