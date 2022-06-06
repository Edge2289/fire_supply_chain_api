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
use catchAdmin\financial\model\PaymentSheetCollectionInfo;
use catchAdmin\financial\model\PaymentSheetSource;
use catchAdmin\purchase\model\PurchaseOrder;
use catcher\base\CatchController;
use catcher\CatchResponse;
use catcher\exceptions\BusinessException;
use think\facade\Db;
use think\Request;
use think\response\Json;

/**
 * Class Payment
 * @package catchAdmin\financial\controller
 * @note 付款单
 */
class Payment extends CatchController
{
    protected $paymentSheetModel;
    protected $paymentSheetSourceModel;
    protected $paymentSheetCollectionInfoModel;

    public function __construct(
        PaymentSheet               $paymentSheetModel,
        PaymentSheetSource         $paymentSheetSourceModel,
        paymentSheetCollectionInfo $paymentSheetCollectionInfoModel
    )
    {
        $this->paymentSheetModel = $paymentSheetModel;
        $this->paymentSheetSourceModel = $paymentSheetSourceModel;
        $this->paymentSheetCollectionInfoModel = $paymentSheetCollectionInfoModel;
    }

    /**
     * TODO 列表
     *
     * @return Json
     * @author 1131191695@qq.com
     */
    public function index()
    {
        $data = $this->paymentSheetModel->getList();
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
        $this->paymentSheetModel->startTrans();
        try {
            $params['payment_time'] = strtotime($params['payment_time']);
            $source = $params['source'];
            $paymentInformation = $params['paymentInformation'];
            unset($params['source'], $params['paymentInformation']);
            // 主表数据
            if (isset($params['id']) && !empty($params['id'])) {
                // 存在id
                $id = $params['id'];
                // 删除旧的数据
                $this->paymentSheetSourceModel->destroy(['payment_sheet_id' => $id]);
                $this->paymentSheetCollectionInfoModel->destroy(['payment_sheet_id' => $id]);
                $this->paymentSheetModel->updateBy($id, $params);
            } else {
                $params['payment_code'] = getCode("PS");
                $id = $this->paymentSheetModel->insertGetId($params);
            }
            /*
             * 源单数据
             */
            // 源单金额
            $sourceAmount = 0;
            $map = [];
            $sourceIds = [];
            foreach ($source as $value) {
                $map[] = [
                    "payment_sheet_id" => $id,
                    "source_id" => $value['id'],
                    "order_date" => $value['order_date'],
                    "type" => $value['type'],
                    "order_code" => $value['order_code'],
                    "amount" => $value['amount'],
                    "remark" => $value['remark'] ?? "",
                ];
                $sourceAmount = bcadd($sourceAmount, $value['amount'], 2);
                $sourceIds[] = $value['id'];
            }
            if (empty($map)) {
                throw new BusinessException("源单数据为空");
            }
            $payAmount = 0;
            $payMap = [];
            /*
             * 支付信息
             */
            foreach ($paymentInformation as $information) {
                if (!$information['payment_code']) {
                    throw new BusinessException("收款信息存在付款账号为空");
                }
                if (!$information['payment_amount']) {
                    throw new BusinessException("收款信息存在付款金额为空");
                }
                if (!$information['payment_type']) {
                    throw new BusinessException("收款信息存在付款方式为空");
                }
                if (!$information['transaction_no']) {
                    throw new BusinessException("收款信息存在交易号为空");
                }
                $payAmount = bcadd($payAmount, $information['payment_amount'], 2);
                $payMap[] = [
                    "payment_sheet_id" => $id,
                    "payment_code" => $information['payment_code'],
                    "payment_amount" => $information['payment_amount'],
                    "payment_type" => $information['payment_type'],
                    "transaction_no" => $information['transaction_no'],
                    "remark" => $information['remark'] ?? "",
                ];
            }
            if (count($map) > 1 && ($payAmount != $sourceAmount)) {
                throw new BusinessException("多源单情况下不允许部分付款");
            }

            /*
             * 新增信息
             */
            $this->paymentSheetCollectionInfoModel->insertAll($payMap);
            $this->paymentSheetSourceModel->insertAll($map);

            /**
             * TODO 新增修改采购单数据
             */
            $table = $params['source_type'] == "purchaseOrder" ? "f_purchase_order" : "f_procurement_warehousing";
            if (count($map) > 1) {
                // 多源单 直接更新数据
                Db::execute("update {$table} set settlement_amount = amount where id in (" . implode(",", $sourceIds) . ")");
            } else {
                // 单源单 更新数据
                Db::execute("update {$table} set settlement_amount = {$sourceAmount} where id in (" . implode(",", $sourceIds) . ")");
            }
            $this->paymentSheetModel->commit();
            return CatchResponse::success(['id' => $id]);
        } catch (\Exception $exception) {
            $this->paymentSheetModel->rollback();
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
        $data = $this->paymentSheetModel->findBy($params['id']);
        if (!$data) {
            return CatchResponse::fail("数据不存在");
        }
        if ($data['audit_status'] == 1) {
            return CatchResponse::fail("付款单已审核,无法修改");
        }
        $this->paymentSheetModel->startTrans();
        try {
            $this->paymentSheetModel->updateBy($params['id'], $params);
            // 修改采购订单状态
            $ids = [];
            foreach ($data->manyPaymentSheetSource as $value) {
                $ids[] = $value['purchase_order_id'];
            }
            if (!empty($ids)) {
                // 修改成已开票
                app(PurchaseOrder::class)->whereIn('id', $ids)->update([
                    'settlement_status' => 1
                ]);
            }
            // 修改当前回款单状态
            $this->paymentSheetModel->commit();
            return CatchResponse::success();
        } catch (\Exception $exception) {
            $this->paymentSheetModel->rollback();
            return CatchResponse::fail();
        }
    }
}