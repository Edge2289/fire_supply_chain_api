<?php
/**
 * Created by PhpStorm.
 * author: xiejiaqing
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
                HeaderItem::label('id')->prop('id'),
                HeaderItem::label('状态')->prop('status_i'),
                HeaderItem::label('采购编号')->prop('purchase_code'),
                HeaderItem::label('采购日期')->prop('purchase_date'),
                HeaderItem::label('采购人员')->prop('user_name'),
                HeaderItem::label('供应商')->prop('supplier_name'),
                HeaderItem::label('商品总数量')->prop('num'),
                HeaderItem::label('入库数量')->prop('put_num'),
                HeaderItem::label('开票状态')->prop('settlement_status'),
                HeaderItem::label('结算类型')->prop('invoice_status'),
                HeaderItem::label('审核状态')->prop('audit_status_i'),
                HeaderItem::label('审核信息')->prop('audit_info'),
                HeaderItem::label('操作')->width(200)->actions([
                    Actions::update("编辑", "editPurchaseOrder"),
                    Actions::normal("导出信息", 'success', "facePrint")->icon('el-icon-printer'),
                ])
            ])
            ->withSearch([
                Search::label('采购编号')->text('company_name', '采购编号'),
                Search::label('结算类型')->text('factory_type', '结算类型'),
                Search::label('审核状态')->select('status', '请选择审核状态',
                    Search::options()->add('全部', '')
                        ->add('未审核', 0)
                        ->add('已审核', 1)
                        ->add('审核失败', 2)
                        ->render()
                ),
                Search::hidden('id', '')
            ])
            ->withApiRoute('factory')
            ->withActions([
                Actions::normal("新增", 'primary', "addPurchaseOrder")->icon('el-icon-plus'),
                Actions::normal("审核", 'primary', "audit")->icon('el-icon-bangzhu'),
                Actions::normal("导出", 'primary', "export")->icon('el-icon-bangzhu'),
            ])
            ->selectionChange()
            ->render();
    }

    protected function form()
    {

    }
}