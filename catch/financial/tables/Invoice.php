<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/3/28
 * Time: 21:05
 */

namespace catchAdmin\financial\tables;


use catcher\CatchTable;
use catcher\library\table\Actions;
use catcher\library\table\HeaderItem;
use catcher\library\table\Search;

/**
 * Class Invoice
 * @package catchAdmin\financial\tables
 */
class Invoice extends CatchTable
{
    protected function table()
    {
        return $this->getTable('invoice')
            ->header([
                HeaderItem::label()->selection(),
                HeaderItem::label('编号')->prop('id'),
                HeaderItem::label('发票单号')->prop('invoice_code'),
                HeaderItem::label('订单类型')->prop('order_type_i'),
                HeaderItem::label('发票时间')->prop('invoice_time'),
                HeaderItem::label('发票金额')->prop('amount'),
                HeaderItem::label('发票类型')->prop('invoice_type_i'),
                HeaderItem::label('备注')->prop('other'),
                HeaderItem::label('操作')->width(200)->actions([
                    Actions::normal("更新", "primary", "handleUpdates", 'el-icon-edit'),
                    Actions::delete()
                ])
            ])
            ->withSearch([
                Search::label('付款类型')->select('payment_type', '回款类型', Search::options()->add('增值税普通发票', "1")
                    ->add('增值税专用发票', "2")
                    ->render()),
                Search::label('付款时间')->datetime('warehouse_name', '仓库名称'),
            ])
            ->withApiRoute('invoice')
            ->withActions([
                Actions::normal("新增", "primary", "handleAdd", "el-icon-plus"),
                Actions::normal("审核", 'primary', "audit")->icon('el-icon-bangzhu'),
            ])
            ->selectionChange()
            ->render();
    }

    protected function form()
    {
    }
}