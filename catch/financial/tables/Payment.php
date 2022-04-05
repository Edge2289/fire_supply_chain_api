<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/2/20
 * Time: 20:13
 */

namespace catchAdmin\financial\tables;


use catcher\CatchTable;
use catcher\library\table\Actions;
use catcher\library\table\HeaderItem;
use catcher\library\table\Search;

/**
 * Class Payment
 * @package catchAdmin\financial\tables
 */
class Payment extends CatchTable
{
    protected function table()
    {
        return $this->getTable('payment')
            ->header([
                HeaderItem::label()->selection(),
                HeaderItem::label('编号')->prop('id'),
                HeaderItem::label('付款单号')->prop('payment_code'),
                HeaderItem::label('付款时间')->prop('payment_time'),
                HeaderItem::label('付款金额')->prop('amount'),
                HeaderItem::label('付款类型')->prop('payment_type'),
                HeaderItem::label('支付方式')->prop('payment_method'),
                HeaderItem::label('备注')->prop('other'),
                HeaderItem::label('操作')->width(200)->actions([
                    Actions::normal("更新", "primary", "handleUpdates", 'el-icon-edit'),
                    Actions::delete()
                ])
            ])
            ->withSearch([
                Search::label('付款类型')->select('payment_type', '回款类型', Search::options()->add('常规', "1")
                    ->add('现金', "2")
                    ->add('尾款', "3")
                    ->add('保证金', "4")
                    ->add('其他', "5")
                    ->render()),
                Search::label('付款时间')->datetime('warehouse_name', '仓库名称'),
            ])
            ->withApiRoute('payment')
            ->withActions([
                Actions::normal("新增", "primary", "handleAdd", "el-icon-plus"),
                Actions::normal("审核", 'primary', "audit")->icon('el-icon-bangzhu')
            ])
            ->selectionChange()
            ->render();
    }

    protected function form()
    {
    }
}