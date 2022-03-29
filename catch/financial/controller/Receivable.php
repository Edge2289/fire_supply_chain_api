<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/2/13
 * Time: 21:20
 */

namespace catchAdmin\financial\controller;


use catchAdmin\financial\model\ReceivableSheet;
use catchAdmin\salesManage\model\SalesOrderModel;
use catcher\base\CatchController;
use catchAdmin\financial\model\Receivable as ReceivableModel;
use catcher\CatchResponse;
use catcher\exceptions\BusinessException;
use think\Request;

/**
 * Class Receivable
 * @package catchAdmin\financial\controller
 * @note 回款单
 */
class Receivable extends CatchController
{
    protected $receivableModel;
    protected $receivableSheet;

    public function __construct(
        ReceivableModel $receivableModel,
        ReceivableSheet $receivableSheet
    )
    {
        $this->receivableModel = $receivableModel;
        $this->receivableSheet = $receivableSheet;
    }

    /**
     * 列表
     *
     * @return \think\response\Json
     * @author 1131191695@qq.com
     */
    public function index()
    {
        return CatchResponse::paginate($this->receivableModel->getList());
    }

    /**
     * 添加
     *
     * @param Request $request
     * @return \think\response\Json
     * @author 1131191695@qq.com
     */
    public function save(Request $request)
    {
        $params = $request->param();
        $this->receivableModel->startTrans();
        try {
            $params['receivable_time'] = strtotime($params['receivable_time']);
            $salesOrder = $params['sales_order'];
            unset($params['sales_order']);
            if (isset($params['id']) && !empty($params['id'])) {
                // 存在id
                $id = $params['id'];
                // 删除旧的数据
                $this->receivableSheet->destroy(['payment_sheet_id' => $id]);
                $this->receivableModel->updateBy($id, $params);
            } else {
                $params['receivable_code'] = getCode("RS");
                $id = $this->receivableModel->createBy($params);
            }
            $map = [];
            foreach ($salesOrder as $value) {
                $map[] = [
                    "receivable_sheet_id" => $id,
                    "sales_order_id" => $value['id'],
                ];
            }
            if (empty($map)) {
                throw new BusinessException("销售订单为空");
            }
            $this->receivableSheet->insertAll($map);
            $this->receivableModel->commit();
            return CatchResponse::success(['id' => $id]);
        } catch (\Exception $exception) {
            $this->receivableModel->rollback();
            return CatchResponse::fail($exception->getMessage());
        }
    }

    /**
     * 审核
     *
     * @param Request $request
     * @return \think\response\Json|void
     * @author 1131191695@qq.com
     */
    public function audit(Request $request)
    {
        $params = $request->param();
        $data = $this->receivableModel->findBy($params['id']);
        if (!$data) {
            return CatchResponse::fail("数据不存在");
        }
        if ($data['audit_status'] == 1) {
            return CatchResponse::fail("回款单已审核,无法修改");
        }
        $this->receivableModel->startTrans();
        try {
            $this->receivableModel->updateBy($params['id'], $params);
            // 修改采购订单状态
            $ids = [];
            foreach ($data->manyReceivableSheet as $value) {
                $ids[] = $value['sales_order_id'];
            }
            if (!empty($ids)) {
                // 修改成已开票
                app(SalesOrderModel::class)->whereIn('id', $ids)->update([
                    'settlement_status' => 1
                ]);
            }
            // 修改销售订单状态
            $this->receivableModel->commit();
            return CatchResponse::success();
        } catch (\Exception $exception) {
            $this->receivableModel->rollback();
            return CatchResponse::fail();
        }
    }
}