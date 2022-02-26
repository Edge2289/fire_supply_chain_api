<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/1/28
 * Time: 19:39
 */

namespace catchAdmin\purchase\tables;


use catcher\CatchTable;
use catcher\library\table\Actions;
use catcher\library\table\HeaderItem;
use catcher\library\table\Search;

/**
 * Class PurchaseOrder
 * @package catchAdmin\purchase\tables
 */
class PurchaseOrder extends CatchTable
{
    protected function table()
    {
        return $this->getTable('purchase')
            ->header([
                HeaderItem::label()->selection(),
//                HeaderItem::label('id')->prop('id'),
                HeaderItem::label('状态')->prop('status_i'),
                HeaderItem::label('采购编号')->prop('purchase_code'),
                HeaderItem::label('供货者')->prop('supplier_name'),
                HeaderItem::label('明细摘要')->prop('detail'),
                HeaderItem::label('总额')->prop('amount'),
                HeaderItem::label('采购日期')->prop('purchase_date'),
                HeaderItem::label('结算类型')->prop('settlement_type_i'),
                HeaderItem::label('备注')->prop('remark'),
                HeaderItem::label('操作')->width(200)->actions([
                    Actions::update("编辑", "editPurchaseOrder"),
                    Actions::normal("复制", 'primary', "copy")->icon('el-icon-document-copy'),
                    Actions::normal("一键入库", 'success', "putStorage"),
//                    Actions::normal("导出信息", 'success', "facePrint")->icon('el-icon-printer'),
                ])
            ])
            ->withSearch([
                Search::label('采购编号')->text('purchase_code', '采购编号'),
                Search::label('结算类型')->text('invoice_status', '结算类型'),
                Search::label('订单状态')->select('audit_status', '请选择审核状态',
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
            ->withApiRoute('purchase')
            ->withActions([
                Actions::normal("新增", 'primary', "addPurchaseOrder")->icon('el-icon-plus'),
                Actions::normal("审核", 'primary', "audit")->icon('el-icon-bangzhu'),
                Actions::normal("结单", 'primary', "audit"),
                Actions::normal("取消结单", 'primary', "audit"),
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