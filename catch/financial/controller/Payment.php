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
        $type = [
            'purchaseOrder' => '采购订单',
            'procurement' => '采购入库单',
        ];
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
            $datum['source_type_name'] = $type[$datum['source_type']] ?? "有误";
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
        if ($params['cope_amount'] < $params['prepaid_amount']) {
            throw new BusinessException("已付金额不能大于应付金额");
        }
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
                    "payment_amount" => count($source) > 1 ? $value['amount'] : $params['prepaid_amount'],
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

            /*
             * 修改采购单数据
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
            /**
             * 第一个循环源单变更结算数据
             *   采购订单的直接判定 结算金额是否一致 以及还有没有其他的付款单
             */
            // 判断是否审核成功
            $this->paymentSheetModel->updateBy($params['id'], [
                'audit_status' => $params['audit_status'],
                'audit_info' => $params['audit_info'],
                'audit_user_id' => request()->user()->id,
                'audit_user_name' => request()->user()->username,
            ]);

            if ($params['audit_status'] == 1) {
                // 审核成功
                // 修改对应的入库单
                $table = $data['source_type'] == "purchaseOrder" ? "f_purchase_order" : "f_procurement_warehousing";
                $purchase_source_id = [];
                foreach ($data->manyPaymentSheetSource as $value) {
                    $model = Db::table($table)->where('id', $value['source_id'])->find();
                    if (!$model) {
                        continue;
                    }
                    $dataMap = $this->paymentSheetModel->alias("ps")
                        ->field('sum(payment_amount) as payment_amount')
                        ->leftJoin("f_payment_sheet_source pss", 'ps.id = pss.payment_sheet_id')
                        ->where('ps.source_type', $data['source_type'])
                        ->where('ps.audit_status', 1)
                        ->where('pss.source_id', $value['source_id'])
                        ->find();
                    $status = 1;
                    if ($dataMap['payment_amount'] == $model['amount']) {
                        // 已结算
                        $status = 2;
                    }
                    Db::table($table)->where('id', $value['source_id'])->update([
                        'settlement_status' => $status
                    ]);
                    if ($data['source_type'] == "procurement") {
                        $purchase_source_id[] = $model['purchase_order_id'];
                    }
                }
                foreach (array_unique($purchase_source_id) as $purchase_order_id) {
                    // 这里是 purchase_order_id
                    $sourceId = Db::table('f_procurement_warehousing')->field('id')->where('purchase_order_id', $purchase_order_id)->find();
                    $purchaseModel = Db::table('f_purchase_order')->where('id', $purchase_order_id)->find();
                    $dataMap = $this->paymentSheetModel->alias("ps")
                        ->field('sum(payment_amount) as payment_amount')
                        ->leftJoin("f_payment_sheet_source pss", 'ps.id = pss.payment_sheet_id')
                        ->where('ps.source_type', $data['source_type'])
                        ->where('ps.audit_status', 1)
                        ->whereIn('pss.source_id', array_column($sourceId, 'id'))
                        ->find();
                    $status = 1;
                    if ($dataMap['payment_amount'] == $purchaseModel['amount']) {
                        // 已结算
                        $status = 2;
                    }
                    // 修改采购订单的数
                    Db::table('f_purchase_order')->where('id', $model['purchase_order_id'])->update([
                        'settlement_status' => $status,
                        'settlement_amount' => $dataMap['payment_amount']
                    ]);
                }
            }

            // 修改当前回款单状态
            $this->paymentSheetModel->commit();
            return CatchResponse::success();
        } catch (\Exception $exception) {
            $this->paymentSheetModel->rollback();
            return CatchResponse::fail();
        }
    }

    /**
     * 作废
     *
     * @param Request $request
     * @return Json
     */
    public function invalid(Request $request)
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
            // 修改采购订单状态
            $sourceIds = [];
            foreach ($data->manyPaymentSheetSource as $value) {
                $sourceIds[] = $value['source_id'];
            }
            $table = $data['source_type'] == "purchaseOrder" ? "f_purchase_order" : "f_procurement_warehousing";
            if (count($sourceIds) > 1) {
                // 多源单 直接更新数据
                Db::execute("update {$table} set settlement_amount = 0 where id in (" . implode(",", $sourceIds) . ")");
            } else {
                // 单源单 更新数据
                Db::execute("update {$table} set settlement_amount = settlement_amount - {$data['prepaid_amount']} where id in (" . implode(",", $sourceIds) . ")");
            }
            if (!empty($ids)) {
                // 修改成已开票
                app(PurchaseOrder::class)->whereIn('id', $ids)->update([
                    'settlement_status' => 1
                ]);
            }
            // 清除数据
            $this->paymentSheetModel->deleteBy($params['id']);
            $this->paymentSheetSourceModel->destroy(['payment_sheet_id' => $params['id']]);
            $this->paymentSheetCollectionInfoModel->destroy(['payment_sheet_id' => $params['id']]);
            // 修改当前回款单状态
            $this->paymentSheetModel->commit();
            return CatchResponse::success();
        } catch (\Exception $exception) {
            $this->paymentSheetModel->rollback();
            return CatchResponse::fail();
        }
    }
}