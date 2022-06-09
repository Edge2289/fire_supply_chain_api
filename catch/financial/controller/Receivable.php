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
use catchAdmin\financial\model\ReceivableSheetCollectionInfo;
use catchAdmin\financial\model\ReceivableSheetSource;
use catchAdmin\salesManage\model\SalesOrderModel;
use catcher\base\CatchController;
use catcher\CatchResponse;
use catcher\exceptions\BusinessException;
use think\facade\Db;
use think\Request;

/**
 * Class Receivable
 * @package catchAdmin\financial\controller
 * @note 回款单
 */
class Receivable extends CatchController
{
    protected $receivableSheet;
    protected $receivableSheetSource;
    protected $receivableSheetCollectionInfo;

    public function __construct(
        ReceivableSheet               $receivableSheet,
        ReceivableSheetSource         $receivableSheetSource,
        ReceivableSheetCollectionInfo $receivableSheetCollectionInfo
    )
    {
        $this->receivableSheet = $receivableSheet;
        $this->receivableSheetSource = $receivableSheetSource;
        $this->receivableSheetCollectionInfo = $receivableSheetCollectionInfo;
    }

    /**
     * 列表
     *
     * @return \think\response\Json
     * @author 1131191695@qq.com
     */
    public function index()
    {
        return CatchResponse::paginate($this->receivableSheet->getList());
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
        $this->receivableSheet->startTrans();
        if ($params['cope_amount'] < $params['prepaid_amount']) {
            throw new BusinessException("已付金额不能大于应付金额");
        }
        try {
            $params['receivable_time'] = strtotime($params['receivable_time']);
            $source = $params['source'];
            $paymentInformation = $params['paymentInformation'];
            unset($params['source'], $params['paymentInformation']);
            // 主表数据
            if (isset($params['id']) && !empty($params['id'])) {
                // 存在id
                $id = $params['id'];
                // 删除旧的数据
                $this->receivableSheetSource->destroy(['receivable_sheet_id' => $id]);
                $this->receivableSheetCollectionInfo->destroy(['receivable_sheet_id' => $id]);
                $this->receivableSheet->updateBy($id, $params);
            } else {
                $params['payment_code'] = getCode("PS");
                $id = $this->receivableSheet->insertGetId($params);
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
                    "receivable_sheet_id" => $id,
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
                    throw new BusinessException("回款信息存在付款账号为空");
                }
                if (!$information['payment_amount']) {
                    throw new BusinessException("回款信息存在付款金额为空");
                }
                if (!$information['payment_type']) {
                    throw new BusinessException("回款信息存在付款方式为空");
                }
                if (!$information['transaction_no']) {
                    throw new BusinessException("回款信息存在交易号为空");
                }
                $payAmount = bcadd($payAmount, $information['payment_amount'], 2);
                $payMap[] = [
                    "receivable_sheet_id" => $id,
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
            $this->receivableSheetCollectionInfo->insertAll($payMap);
            $this->receivableSheetSource->insertAll($map);

            /*
             * 修改源单数据
             */
            $table = $params['source_type'] == "salesOrder" ? "f_sales_order" : "f_outbound_order";
            if (count($map) > 1) {
                // 多源单 直接更新数据
                Db::execute("update {$table} set settlement_amount = amount where id in (" . implode(",", $sourceIds) . ")");
            } else {
                // 单源单 更新数据
                Db::execute("update {$table} set settlement_amount = {$sourceAmount} where id in (" . implode(",", $sourceIds) . ")");
            }
            $this->receivableSheet->commit();
            return CatchResponse::success(['id' => $id]);
        } catch (\Exception $exception) {
            $this->receivableSheet->rollback();
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
        $data = $this->receivableSheet->findBy($params['id']);
        if (!$data) {
            return CatchResponse::fail("数据不存在");
        }
        if ($data['audit_status'] == 1) {
            return CatchResponse::fail("付款单已审核,无法修改");
        }
        $this->receivableSheet->startTrans();

        try {
            // 判断是否审核成功
            $this->receivableSheet->updateBy($params['id'], [
                'audit_status' => $params['audit_status'],
                'audit_info' => $params['audit_info'],
                'audit_user_id' => request()->user()->id,
                'audit_user_name' => request()->user()->username,
            ]);

            if ($params['audit_status'] == 1) {
                // 审核成功
                // 修改对应的入库单
                $table = $data['source_type'] == "salesOrder" ? "f_sales_order" : "f_outbound_order";
                $sales_source_id = [];
                foreach ($data->manyPaymentSheetSource as $value) {
                    $model = Db::table($table)->where('id', $value['source_id'])->find();
                    if (!$model) {
                        continue;
                    }
                    $dataMap = $this->receivableSheet->alias("ps")
                        ->field('sum(payment_amount) as payment_amount')
                        ->leftJoin("f_receivable_sheet_source rss", 'ps.id = rss.receivable_sheet_id')
                        ->where('ps.source_type', $data['source_type'])
                        ->where('ps.audit_status', 1)
                        ->where('rss.source_id', $value['source_id'])
                        ->find();
                    $status = 1;
                    if ($dataMap['payment_amount'] == $model['amount']) {
                        // 已结算
                        $status = 2;
                    }
                    Db::table($table)->where('id', $value['source_id'])->update([
                        'settlement_status' => $status
                    ]);
                    if ($data['source_type'] == "salesOrder") {
                        $sales_source_id[] = $model['sales_order_id'];
                    }
                }
                foreach (array_unique($sales_source_id) as $sales_order_id) {
                    $sourceId = Db::table('f_outbound_order')->field('id')->where('sales_order_id', $sales_order_id)->get();
                    $purchaseModel = Db::table('f_sales_order')->where('id', $sales_order_id)->find();
                    $dataMap = $this->receivableSheet->alias("ps")
                        ->field('sum(payment_amount) as payment_amount')
                        ->leftJoin("f_receivable_source rss", 'ps.id = rss.receivable_sheet_id')
                        ->where('ps.source_type', $data['source_type'])
                        ->where('ps.audit_status', 1)
                        ->whereIn('rss.source_id', array_column($sourceId, 'id'))
                        ->find();
                    $status = 1;
                    if ($dataMap['payment_amount'] == $purchaseModel['amount']) {
                        // 已结算
                        $status = 2;
                    }
                    // 修改采购订单的数
                    Db::table('f_sales_order')->where('id', $sales_order_id)->update([
                        'settlement_status' => $status,
                        'settlement_amount' => $dataMap['payment_amount']
                    ]);
                }
            }

            // 修改当前回款单状态
            $this->receivableSheet->commit();
            return CatchResponse::success();
        } catch (\Exception $exception) {
            $this->receivableSheet->rollback();
            return CatchResponse::fail();
        }
    }

    /**
     * 删除回款单
     * @param Request $request
     * @return \think\response\Json
     * @author 1131191695@qq.com
     */
    public function delete(Request $request)
    {
        $id = $request->param("id");
        $this->receivableSheet->startTrans();
        try {
            $data = $this->receivableSheet->getFindByKey($id);
            if (!$data) {
                return CatchResponse::fail("数据不存在");
            }
            if ($data['audit_status'] == 1) {
                return CatchResponse::fail("回款单已审核,无法修改");
            }
            // 修改采购订单状态
            $sourceIds = [];
            foreach ($data->manyPaymentSheetSource as $value) {
                $sourceIds[] = $value['source_id'];
            }
            $table = $data['source_type'] == "salesOrder" ? "f_sales_order" : "f_outbound_order";
            if (count($sourceIds) > 1) {
                // 多源单 直接更新数据
                Db::execute("update {$table} set settlement_amount = 0 where id in (" . implode(",", $sourceIds) . ")");
            } else {
                // 单源单 更新数据
                Db::execute("update {$table} set settlement_amount = settlement_amount - {$data['prepaid_amount']} where id in (" . implode(",", $sourceIds) . ")");
            }
            if (!empty($ids)) {
                // 修改成已开票
                app(SalesOrderModel::class)->whereIn('id', $ids)->update([
                    'settlement_status' => 1
                ]);
            }
            // 清除数据
            $this->receivableSheet->deleteBy($id);
            $this->receivableSheetSource->destroy(['receivable_sheet_id' => $id]);
            $this->receivableSheetCollectionInfo->destroy(['receivable_sheet_id' => $id]);
            // 修改当前回款单状态
            $this->receivableSheet->commit();
            return CatchResponse::success();
        } catch (\Exception $exception) {
            $this->receivableSheet->rollback();
            return CatchResponse::fail();
        }
    }
}