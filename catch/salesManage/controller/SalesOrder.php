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

    public function getList()
    {
        return CatchResponse::paginate($this->salesOrderModel->getList());
    }

    // 保存或者更新
    public function save(Request $request)
    {
        $params = $request->param();
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