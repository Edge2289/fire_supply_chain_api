<?php
/**
 * Created by PhpStorm.
 * author: xiejiaqing
 * Note: Tired as a dog
 * Date: 2022/2/20
 * Time: 20:13
 */

namespace catchAdmin\financial\tables;


use catchAdmin\financial\tables\forms\Factory;
use catcher\CatchTable;
use catcher\library\table\Actions;
use catcher\library\table\HeaderItem;
use catcher\library\table\Search;

/**
 * Class Receivable
 * @package catchAdmin\financial\tables
 */
class Receivable extends CatchTable
{
    protected function table()
    {
        return $this->getTable('receivable')
            ->header([
                HeaderItem::label()->selection(),
                HeaderItem::label('编号')->prop('id'),
                HeaderItem::label('回款单号')->prop('receivable_code'),
                HeaderItem::label('回款时间')->prop('receivable_time'),
                HeaderItem::label('回款金额')->prop('amount'),
                HeaderItem::label('回款类型')->prop('payment_type'),
                HeaderItem::label('支付方式')->prop('payment_method'),
                HeaderItem::label('备注')->prop('other'),
                HeaderItem::label('操作')->width(200)->actions([
                    Actions::update(), Actions::delete()
                ])
            ])
            ->withSearch([
                Search::label('回款类型')->select('payment_type', '回款类型', Search::options()->add('常规', "1")
                    ->add('现金', "2")
                    ->add('尾款', "3")
                    ->add('保证金', "4")
                    ->add('其他', "5")
                    ->render()),
                Search::label('回款时间')->datetime('warehouse_name', '仓库名称'),
            ])
            ->withApiRoute('receivable')
            ->withActions([
                Actions::normal("新增", "primary", "handleAdd", "el-icon-plus")
            ])
            ->selectionChange()
            ->render();
    }

    protected function form()
    {
    }
}