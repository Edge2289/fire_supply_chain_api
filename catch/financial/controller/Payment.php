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
use catchAdmin\purchase\model\PurchaseOrder;
use catcher\base\CatchController;
use catchAdmin\financial\model\Payment as PaymentModel;
use catcher\CatchResponse;
use catcher\exceptions\BusinessException;
use think\Request;
use think\response\Json;

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
     * @return Json
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
     * @return Json
     * @author 1131191695@qq.com
     */
    public function save(Request $request)
    {
        $params = $request->param();
        $this->paymentModel->startTrans();
        try {
            $params['payment_time'] = strtotime($params['payment_time']);
            $purchaseOrder = $params['purchase_order'];
            unset($params['purchase_order']);
            if (isset($params['id']) && !empty($params['id'])) {
                // 存在id
                $id = $params['id'];
                // 删除旧的数据
                $this->paymentSheetModel->destroy(['payment_sheet_id' => $id]);
                $this->paymentModel->updateBy($id, $params);
            } else {
                $params['payment_code'] = getCode("PS");
                $id = $this->paymentModel->createBy($params);
            }
            $map = [];
            foreach ($purchaseOrder as $value) {
                $map[] = [
                    "payment_sheet_id" => $id,
                    "purchase_order_id" => $value['id'],
                ];
            }
            if (empty($map)) {
                throw new BusinessException("采购订单为空");
            }
            $this->paymentSheetModel->insertAll($map);
            $this->paymentModel->commit();
            return CatchResponse::success(['id' => $id]);
        } catch (\Exception $exception) {
            $this->paymentModel->rollback();
            return CatchResponse::fail($exception->getMessage());
        }
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
        $params = $request->param();
        $data = $this->paymentModel->findBy($params['id']);
        if (!$data) {
            return CatchResponse::fail("数据不存在");
        }
        if ($data['audit_status'] == 1) {
            return CatchResponse::fail("付款单已审核,无法修改");
        }
        $this->paymentModel->startTrans();
        try {
            $this->paymentModel->updateBy($params['id'], $params);
            // 修改采购订单状态
            $ids = [];
            foreach ($data->manyPaymentSheet as $value) {
                $ids[] = $value['purchase_order_id'];
            }
            if (!empty($ids)) {
                // 修改成已开票
                app(PurchaseOrder::class)->whereIn('id', $ids)->update([
                    'settlement_status' => 1
                ]);
            }
            // 修改当前回款单状态
            $this->paymentModel->commit();
            return CatchResponse::success();
        } catch (\Exception $exception) {
            $this->paymentModel->rollback();
            return CatchResponse::fail();
        }
    }
}