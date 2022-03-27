<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/3/20
 * Time: 19:30
 */

namespace catchAdmin\salesManage\tables;


use catcher\CatchTable;
use catcher\library\table\Actions;
use catcher\library\table\HeaderItem;
use catcher\library\table\Search;
use fire\enums\action\NormalEnums;

/**
 * Class OutboundOrder
 * @package catchAdmin\salesManage\tables
 */
class OutboundOrder extends CatchTable
{
    protected function table()
    {
        return $this->getTable('outboundOrder')
            ->header([
                HeaderItem::label()->selection(),
                HeaderItem::label('状态')->prop('status_i'),
                HeaderItem::label('出库编号')->prop('outbound_order_code')->width(130),
                HeaderItem::label('供货者')->prop('supplier_name'),
                HeaderItem::label('客户')->prop('customer_name'),
                HeaderItem::label('明细摘要')->prop('detail'),
                HeaderItem::label('总额')->prop('amount'),
                HeaderItem::label('出库日期')->prop('outbound_time'),
                HeaderItem::label('物流公司')->prop('logistics_code'),
                HeaderItem::label('物流单号')->prop('logistics_number'),
                HeaderItem::label('审核状态')->prop('audit_status_i'),
                HeaderItem::label('备注')->prop('remark'),
                HeaderItem::label('操作')->width(180)->actions([
                    Actions::update("编辑", "edit"),
                    Actions::normal("物流", NormalEnums::$warning, "logistics")->icon('el-icon-document-copy'),
                ])
            ])
            ->withSearch([
                Search::label('订单编号')->text('order_code', '订单编号'),
                Search::label('结算类型')->text('invoice_status', '结算类型'),
                Search::label('订单状态')->select('status', '请选择状态',
                    Search::options()->add('全部', '')
                        ->add('未完成', 0)
                        ->add('已完成', 1)
                        ->add('作废', 2)
                        ->render()
                ), Search::label('审核状态')->select('audit_status', '请选择审核状态',
                    Search::options()->add('全部', '')
                        ->add('未审核', 0)
                        ->add('已审核', 1)
                        ->add('审核失败', 2)
                        ->render()
                ),
                Search::hidden('id', '')
            ])
            ->withApiRoute('outboundOrder')
            ->withActions([
                Actions::normal("新增", 'primary', "add")->icon('el-icon-plus'),
                Actions::normal("审核", 'primary', "audit")->icon('el-icon-bangzhu'),
                Actions::normal("发票", 'primary', "invoice"),
                Actions::normal("作废", 'primary', "cancel"),
                Actions::normal("导出", 'primary', "export"),
            ])
            ->selectionChange()
            ->render();
    }

    protected function form()
    {

    }
}