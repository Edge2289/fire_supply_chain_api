<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/3/12
 * Time: 09:49
 */

namespace catchAdmin\salesManage\tables;


use catcher\CatchTable;
use catcher\library\table\Actions;
use catcher\library\table\HeaderItem;
use catcher\library\table\Search;

/**
 * Class SalesOrder
 * @package catchAdmin\salesManage\tables
 */
class SalesOrder extends CatchTable
{
    protected function table()
    {
        return $this->getTable('salesOrder')
            ->header([
                HeaderItem::label()->selection(),
//                HeaderItem::label('id')->prop('id'),
                HeaderItem::label('状态')->prop('status_i'),
                HeaderItem::label('订单编号')->prop('order_code'),
                HeaderItem::label('供货者')->prop('supplier_name'),
                HeaderItem::label('客户')->prop('supplier_name'),
                HeaderItem::label('明细摘要')->prop('detail'),
                HeaderItem::label('总额')->prop('amount'),
                HeaderItem::label('销售日期')->prop('sales_time'),
                HeaderItem::label('结算类型')->prop('settlement_type_i'),
                HeaderItem::label('备注')->prop('remark'),
                HeaderItem::label('操作')->width(200)->actions([
                    Actions::update("编辑", "editPurchaseOrder"),
//                    Actions::normal("复制", 'primary', "copy")->icon('el-icon-document-copy'),
                    Actions::normal("出库/发货", 'success', "outbound"),
                ])
            ])
            ->withSearch([
                Search::label('订单编号')->text('order_code', '订单编号'),
                Search::label('结算类型')->text('invoice_status', '结算类型'),
                Search::label('订单状态')->select('audit_status', '请选择状态',
                    Search::options()->add('全部', '')
                        ->add('未完成', 0)
                        ->add('已完成', 1)
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
            ->withApiRoute('salesOrder')
            ->withActions([
                Actions::normal("新增", 'primary', "addPurchaseOrder")->icon('el-icon-plus'),
                Actions::normal("审核", 'primary', "audit")->icon('el-icon-bangzhu'),
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