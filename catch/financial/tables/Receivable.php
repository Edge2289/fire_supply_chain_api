<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
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
                HeaderItem::label('源单类型')->prop('source_type_name'),
                HeaderItem::label('回款时间')->prop('receivable_time'),
                HeaderItem::label('回款金额')->prop('prepaid_amount'),
                HeaderItem::label('备注')->prop('other'),
                HeaderItem::label('操作')->width(200)->actions([
                    Actions::update(), Actions::delete()
                ])
            ])
            ->withSearch([
                Search::label('源单类型')->select('source_type', '源单类型',
                    Search::options()
                        ->add('销售订单', "salesOrder")
                        ->add('销售出库单', "outboundOrder")
                        ->render()),
                Search::label('回款单号')->text('receivable_code', '付款单号'),
            ])
            ->withApiRoute('receivable')
            ->withActions([
                Actions::normal("新增", "primary", "handleAdd", "el-icon-plus"),
                Actions::normal("审核", 'primary', "audit")->icon('el-icon-bangzhu'),
                Actions::normal("作废", 'primary', "invalid")->icon('el-icon-bangzhu'),
            ])
            ->selectionChange()
            ->render();
    }

    protected function form()
    {
    }
}