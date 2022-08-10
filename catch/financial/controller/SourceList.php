<?php
/**
 * Created by PhpStorm.
 * author: meridian(1131191695@qq.com)
 * Note: Tired as a dog
 * Date: 2022/5/25
 * Time: 11:08
 */

namespace catchAdmin\financial\controller;


use app\Request;
use catchAdmin\purchase\model\ProcurementWarehousing;
use catchAdmin\purchase\model\PurchaseOrder;
use catchAdmin\salesManage\model\OutboundOrder;
use catchAdmin\salesManage\model\SalesOrderModel;
use catcher\base\CatchController;
use catcher\CatchResponse;
use fire\data\ChangeStatus;

/**
 * Class SourceList
 * @package catchAdmin\financial\controller
 */
class SourceList extends CatchController
{
    public function index(Request $request)
    {
        $type = $request->get('type');
        if (!method_exists($this, $type)) {
            return CatchResponse::fail("不存在有效的类型");
        }
        $data = $this->{$type}();
        ChangeStatus::getInstance()->settlementStatus()->auditStatus()->handle($data);
        return CatchResponse::paginate($data);
    }

    /**
     * 采购订单
     * @return mixed
     */
    public function purchaseOrder()
    {
        $data = PurchaseOrder::field(['id', '(amount - settlement_amount) amount', '"采购订单" as type', 'audit_status', 'purchase_code as order_code', 'purchase_date as order_date', 'settlement_status'])
            ->whereRaw("(amount - settlement_amount) > 0")
            ->whereAuditStatus(1)  // 审核
            ->whereSettlementType(0) // 现结
            ->whereSettlementStatus(0) //
            ->whereSupplierId(app(Request::class)->get('customer_info_id'))
            ->catchSearch()->order("id desc")
            ->paginate();
        foreach ($data as &$datum) {
            $details = $datum->hasPurchaseOrderDetails;
            $datum['details'] = $this->getDetails($datum, $details);
            unset($datum->hasPurchaseOrderDetails);
        }
        return $data;
    }

    /**
     * 入库单
     *
     * @return \think\Paginator
     * @throws \think\db\exception\DbException
     */
    public function procurement()
    {
        $procurement = new ProcurementWarehousing();
        $data = $procurement->alias('pw')
            ->field(['pw.id', '(pw.amount - pw.settlement_amount) amount', '"采购入库单" as type', 'pw.audit_status', 'pw.warehouse_entry_code as order_code', 'pw.inspection_date as order_date', 'pw.settlement_status'])
            ->leftJoin("f_purchase_order po", 'po.id = pw.purchase_order_id')
            ->whereRaw("(pw.amount - pw.settlement_amount) > 0")
            ->where('pw.audit_status', 1)  // 审核
            ->where('po.settlement_type', 1) // 月结
            ->where('pw.settlement_status', 0) // 结算状态
            ->where('pw.supplier_id', app(Request::class)->get('customer_info_id'))
            ->catchSearch([], 'pw')->order("or.id desc")
            ->paginate();
        foreach ($data as &$datum) {
            $details = $datum->hasOutboundOrderDetails;
            $datum['details'] = $this->getDetails($datum, $details);
            unset($datum->hasOutboundOrderDetails);
        }
        return $data;
    }

    /**
     * 销售订单
     *
     * @return mixed
     */
    public function salesOrder()
    {
        // 需要订单携带商品数据
        $data = app(SalesOrderModel::class)
            ->field(['id', '(amount - settlement_amount) as amount', '"销售订单" as type', 'audit_status', 'order_code', 'sales_time as order_date', 'settlement_status'])
            ->whereRaw("(amount - settlement_amount) > 0")
            ->whereAuditStatus(1)  // 审核
            ->whereSettlementType(0) // 现结
            ->whereSettlementStatus(0) //
            ->whereCustomerInfoId(app(Request::class)->get('customer_info_id'))
            ->catchSearch()->order("id desc")
            ->paginate();
        foreach ($data as &$datum) {
            $details = $datum->hasSalesOrderDetails;
            $datum['details'] = $this->getDetails($datum, $details);
            unset($datum->hasSalesOrderDetails);
        }
        return $data;
    }

    /**
     * 出库单
     *
     * @return \think\Paginator
     * @throws \think\db\exception\DbException
     */
    public function outboundOrder()
    {
        $outboundOrderModel = new OutboundOrder();
        $data = $outboundOrderModel
            ->alias('or')
            ->field(['or.id', '"销售出库单" as type', '(r.amount - or.settlement_amount) as amount', 'or.audit_status', 'so.outbound_order_code as order_code', 'outbound_time as order_date', 'so.settlement_status'])
            ->leftJoin("f_sales_order so", 'so.id = or.sales_order_id')
            ->where('or.audit_status', 1)  // 审核
            ->where('so.settlement_type', 1) // 月结
            ->where('so.settlement_status', '<>', 2) // 未结
            ->whereRaw("(or.amount - or.settlement_amount) > 0")
            ->where('or.customer_info_id', app(Request::class)->get('customer_info_id'))
            ->catchSearch([], 'or')->order("or.id desc")
            ->paginate();
        foreach ($data as &$datum) {
            $details = $datum->hasOutboundOrderDetails;
            $datum['details'] = $this->getDetails($datum, $details);
            unset($datum->hasOutboundOrderDetails);
        }
        return $data;
    }

    /**
     * 获取详情
     *
     * @param $datum
     * @param $details
     * @return array
     */
    private function getDetails($datum, $details)
    {
        $map = [];
        foreach ($details as $detail) {
            $p['order_code'] = $datum->order_code;
            $p['item_number'] = $detail->item_number;
            $p['sku_code'] = $detail->sku_code;
            $p['quantity'] = $detail->quantity;
            $p['unit_price'] = $detail->unit_price;
            $p['entity'] = $detail->entity;
            $p['product_name'] = $detail->hasProductData->product_name;
            $p['product_code'] = $detail->hasProductSkuData->product_code;
            // 单价*（1+税率%）=价税合计
            $p['levied_total'] = bcmul($detail->unit_price, bcadd(1, ($detail->tax_rate / 100), 2), 2);
            $map[] = $p;
        }
        return $map;
    }

    /** --- 返回的格式 --- */

    /**
     * 订单日期
     * 订单编号
     * 审核状态
     * 付款状态
     * 订单金额
     */
    /**
     * 订单编号
     * 产品编码
     * 产品名称
     * 规格
     * 型号
     * 数量
     * 单价
     * 价税合计
     */

}