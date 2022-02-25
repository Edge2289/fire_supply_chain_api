<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/2/24
 * Time: 21:32
 */

namespace catchAdmin\purchase\tables;


use catcher\CatchTable;
use catcher\library\table\Actions;
use catcher\library\table\HeaderItem;
use catcher\library\table\Search;

/**
 * Class ProcurementWarehousing
 * @package catchAdmin\purchase\tables
 */
class ProcurementWarehousing extends CatchTable
{
    protected function table()
    {
        return $this->getTable('procurementWare')
            ->header([
                HeaderItem::label()->selection(),
                HeaderItem::label('入库编号')->prop('purchase_code'),
                HeaderItem::label('入库日期')->prop('purchase_date'),
                HeaderItem::label('仓库')->prop('settlement_type'),
                HeaderItem::label('入库数量')->prop('put_num'),
                HeaderItem::label('状态')->prop('status_i'),
                HeaderItem::label('审核状态')->prop('audit_status_i'),
                HeaderItem::label('备注')->prop('remark'),
                HeaderItem::label('操作')->width(200)->actions([
                    Actions::update("编辑", "editPurchaseOrder"),
                    Actions::normal("审核", 'primary', "audit")->icon('el-icon-bangzhu'),
                    Actions::normal("作废", 'primary', "cancel"),
                ])
            ])
            ->withSearch([
                Search::label('入库编号')->text('purchase_code', '采购编号'),
                Search::label('仓库')->text('warehouse_id', '仓库'),
                Search::label('状态')->select('audit_status', '请选择审核状态',
                    Search::options()->add('全部', '')
                        ->add('未完成', 0)
                        ->add('已完1成', 1)
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
            ->withApiRoute('procurementWare')
            ->withActions([
                Actions::normal("新增", 'primary', "addPurchaseOrder")->icon('el-icon-plus'),
            ])
            ->selectionChange()
            ->render();
    }

    protected function form()
    {

    }
}