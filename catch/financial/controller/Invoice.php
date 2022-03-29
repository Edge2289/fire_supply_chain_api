<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/2/13
 * Time: 21:21
 */

namespace catchAdmin\financial\controller;


use catchAdmin\financial\model\InvoiceSheet;
use catchAdmin\purchase\model\PurchaseOrder;
use catchAdmin\salesManage\model\OutboundOrder;
use catcher\base\CatchController;
use catchAdmin\financial\model\Invoice as InvoiceModel;
use catcher\CatchResponse;
use catcher\exceptions\BusinessException;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\Request;
use think\response\Json;

/**
 * Class Invoice
 * @package catchAdmin\financial\controller
 * @note 开票
 */
class Invoice extends CatchController
{
    protected $invoiceModel;
    protected $invoiceSheet;

    public function __construct(
        InvoiceModel $invoiceModel,
        InvoiceSheet $invoiceSheet
    )
    {
        $this->invoiceModel = $invoiceModel;
        $this->invoiceSheet = $invoiceSheet;
    }

    /**
     * 列表
     *
     * @return Json
     * @throws DbException
     * @author 1131191695@qq.com
     */
    public function index()
    {
        $data = $this->invoiceModel->getList();
        foreach ($data as &$datum) {
            $datum['order_type_i'] = $datum['order_type'] == 1 ? "采购订单" : "出库订单";
            $datum['invoice_type_i'] = $datum['invoice_type'] == 1 ? "增值税普通发票" : "增值税专用发票";
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
        if (empty($params)) {
            throw new BusinessException("参数缺失");
        }
        $this->invoiceModel->startTrans();
        try {
            $params['invoice_time'] = strtotime($params['invoice_time']);
            $outboundOrder = $params['outbound_order'];
            unset($params['purchase_order']);
            $params['invoice_code'] = getCode("IO");
            if (isset($params['id']) && !empty($params['id'])) {
                // 存在id
                $id = $params['id'];
                // 删除旧的数据
                $this->invoiceSheet->destroy(['payment_sheet_id' => $id]);
                $this->invoiceModel->updateBy($id, $params);
            } else {
                $params['receivable_code'] = getCode("RS");
                $id = $this->invoiceModel->createBy($params);
            }
            $invoiceSheet = [];
            foreach (array_column($outboundOrder, "id") as $value) {
                $invoiceSheet[] = [
                    'invoice_sheet_id' => $id,
                    'order_id' => $value
                ];
            }
            if (empty($invoiceSheet)) {
                throw new BusinessException("出库订单为空");
            }
            $this->invoiceSheet->insertAll($invoiceSheet);
            $this->invoiceModel->commit();
            return CatchResponse::success();
        } catch (\Exception $exception) {
            $this->invoiceModel->rollback();
            return CatchResponse::fail();
        }
    }

    /**
     * 删除发票
     *
     * @param Request $request
     * @return Json
     * @author 1131191695@qq.com
     */
    public function delete(Request $request)
    {
        $params = $request->param();
        $data = $this->invoiceModel->findBy($params['id']);
        if (!$data) {
            return CatchResponse::fail("数据不存在");
        }
        if ($data['audit_status'] == 1) {
            return CatchResponse::fail("发票已审核,无法删除");
        }
        $this->invoiceModel->deleteBy($params['id']);
        return CatchResponse::success();
    }

    /**
     * 审核
     *
     * @param Request $request
     * @return Json
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author 1131191695@qq.com
     */
    public function audit(Request $request)
    {
        $params = $request->param();
        $data = $this->invoiceModel->findBy($params['id']);
        if (!$data) {
            return CatchResponse::fail("数据不存在");
        }
        if ($data['audit_status'] == 1) {
            return CatchResponse::fail("发票已审核,无法再次审核");
        }
        if ($this->invoiceModel->updateBy($params['id'], $params)) {
            return CatchResponse::success();
        }
        $ids = [];
        foreach ($data->manyInvoiceSheet as $value) {
            $ids[] = $value['outbound_order_id'];
        }
        if (!empty($ids)) {
            // 修改成已开票
            if ($data['order_type'] == 1) {
                // 采购
                app(PurchaseOrder::class)->whereIn('id', $ids)->update([
                    'invoice_status' => 1
                ]);
            } else {
                // 出库单
                app(OutboundOrder::class)->whereIn('id', $ids)->update([
                    'invoice_status' => 1
                ]);
            }
        }
        // 修改出库单的开票状态 invoice_status
        return CatchResponse::fail();
    }
}