<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/3/28
 * Time: 16:20
 */

namespace catchAdmin\inventory\controller;


use app\Request;
use catchAdmin\inventory\model\OtherPutInventoryDetails;
use catcher\base\CatchController;
use catcher\CatchResponse;
use catchAdmin\inventory\model\OtherPutInventory as OtherPutInventoryModel;
use catcher\exceptions\BusinessException;
use think\response\Json;

/**
 * 其他出库
 *
 * Class OtherPutInventory
 * @package catchAdmin\inventory\controller
 */
class OtherPutInventory extends CatchController
{
    protected $warehouse;
    protected $otherPutInventoryModel;
    protected $otherPutInventoryDetails;

    public function __construct(
        Warehouse                $warehouse,
        OtherPutInventoryModel   $otherPutInventoryModel,
        OtherPutInventoryDetails $otherPutInventoryDetails
    )
    {
        $this->warehouse = $warehouse;
        $this->otherPutInventoryModel = $otherPutInventoryModel;
        $this->otherPutInventoryDetails = $otherPutInventoryDetails;
    }

    /**
     * 列表
     * @return Json
     * @author 1131191695@qq.com
     */
    public function index()
    {
        return CatchResponse::paginate($this->otherPutInventoryModel->getList());
    }

    public function save(Request $request)
    {
        $params = $request->param();
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
        $this->otherPutInventoryModel->startTrans();
        try {
            $purchaseOrderModel = $this->otherPutInventoryModel->getFindByKey($params['id']);
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
            $b = $this->otherPutInventoryModel->updateBy($params['id'], [
                'audit_status' => $params['audit_status'],
                'audit_info' => $params['audit_info'],
                'audit_user_id' => request()->user()->id,
                'audit_user_name' => request()->user()->username,
            ]);
            if (!$b) {
                throw new \Exception("审核失败");
            }
            $this->otherPutInventoryModel->commit();
        } catch (\Exception $exception) {
            $this->otherPutInventoryModel->rollback();
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
        $this->otherPutInventoryModel->startTrans();
        // 更新
        try {
            // 添加事务 排他锁
            $purchaseOrderModel = $this->otherPutInventoryModel->getFindByKey($params['id']);
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
            $this->otherPutInventoryModel->commit();
        } catch (\Exception $exception) {
            $this->otherPutInventoryModel->rollback();
            throw new BusinessException($exception->getMessage());
        }
        return CatchResponse::success();
    }
}