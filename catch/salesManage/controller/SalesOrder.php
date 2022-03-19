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
                $id = $this->salesOrderModel->createBy($params);
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