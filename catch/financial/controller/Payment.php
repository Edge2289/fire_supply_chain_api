<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/2/13
 * Time: 21:20
 */

namespace catchAdmin\financial\controller;


use catchAdmin\financial\model\PaymentSheet;
use catcher\base\CatchController;
use catchAdmin\financial\model\Payment as PaymentModel;
use catcher\CatchResponse;
use think\Request;

/**
 * Class Payment
 * @package catchAdmin\financial\controller
 * @note 付款单
 */
class Payment extends CatchController
{
    protected $paymentModel;
    protected $paymentSheetModel;

    public function __construct(
        PaymentModel $paymentModel,
        PaymentSheet $paymentSheetModel
    )
    {
        $this->paymentModel = $paymentModel;
        $this->paymentSheetModel = $paymentSheetModel;
    }

    /**
     * 列表
     *
     * @return \think\response\Json
     * @author 1131191695@qq.com
     */
    public function index()
    {
        $data = $this->paymentModel->getList();
        foreach ($data as &$datum) {
            $map = [];
            foreach ($datum['manyPurchaserOrder'] ?: [] as $value) {
                $hasSupplierLicense = [
                    'id' => $value['hasPurchaseOrder']['hasSupplierLicense']['id'],
                    'company_name' => $value['hasPurchaseOrder']['hasSupplierLicense']['company_name'],
                ];
                unset($value['hasPurchaseOrder']['hasSupplierLicense']);
                $value['hasPurchaseOrder']['hasSupplierLicense'] = $hasSupplierLicense;
                $map[] = $value['hasPurchaseOrder'];
            }
            unset($datum['manyPurchaserOrder']);
            $datum['hasPurchaseOrder'] = $map;
        }
        return CatchResponse::paginate($data);
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
        $this->paymentModel->startTrans();
        try {
            $params['payment_time'] = strtotime($params['payment_time']);
            $purchaseOrder = $params['purchase_order'];
            $params['payment_code'] = getCode("PS");
            unset($params['purchase_order']);
            $pk = $this->paymentModel->createBy($params);
            $purchaseMap = [];
            foreach ($purchaseOrder as $value) {
                $purchaseMap[] = [
                    "payment_sheet_id" => $pk,
                    "purchase_order_id" => $value['id'],
                ];
            }
            $this->paymentSheetModel->insertAll($purchaseMap);
            $this->paymentModel->commit();
            return CatchResponse::success(['id' => $pk]);
        } catch (\Exception $exception) {
            $this->paymentModel->rollback();
            return CatchResponse::fail($exception->getMessage());
        }
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
        $params = $request->param();
        if ($params['id']) {
            return CatchResponse::fail("未选择回款单");
        }
        $data = $this->paymentModel->findBy($params['id']);
        if (!$data) {
            return CatchResponse::fail("数据不存在");
        }
        if ($data['audit_status'] == 1) {
            return CatchResponse::fail("回款单已审核,无法修改");
        }
        if ($this->paymentModel->updateBy($params['id'], $params)) {
            return CatchResponse::success();
        }
        return CatchResponse::fail();
    }

    /**
     * 审核
     *
     * @param Request $request
     * @return \think\response\Json|void
     * @author 1131191695@qq.com
     */
    public function audio(Request $request)
    {
        $params = $request->param();
        $data = $this->paymentModel->findBy($params['id']);
        if (!$data) {
            return CatchResponse::fail("数据不存在");
        }
        if ($data['audit_status'] == 1) {
            return CatchResponse::fail("回款单已审核,无法修改");
        }
        // 修改采购订单状态
        // 修改当前回款单状态
    }
}