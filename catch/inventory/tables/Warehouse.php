<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/1/11
 * Time: 21:35
 */

namespace catchAdmin\inventory\tables;


use catchAdmin\inventory\tables\forms\Factory;
use catcher\CatchTable;
use catcher\library\table\Actions;
use catcher\library\table\HeaderItem;
use catcher\library\table\Search;

/**
 * Class Warehouse
 * @package catchAdmin\inventory\tables
 */
class Warehouse extends CatchTable
{
    protected function table()
    {
        return $this->getTable('warehouse')
            ->header([
                HeaderItem::label()->selection(),
                HeaderItem::label('id')->prop('id'),
                HeaderItem::label('仓库编码')->prop('warehouse_code'),
                HeaderItem::label('仓库名称')->prop('warehouse_name'),
                HeaderItem::label('仓库类别')->prop('warehouse_type_name'),
                HeaderItem::label('仓库地址')->prop('address'),
                HeaderItem::label('联系人')->prop('contact'),
                HeaderItem::label('联系电话')->prop('contact_phone'),
                HeaderItem::label('备注')->prop('note'),
                HeaderItem::label('操作')->width(200)->actions([
                    Actions::update(), Actions::delete()
                ])
            ])
            ->withSearch([
                Search::label('仓库编码')->text('warehouse_code', '仓库编码'),
                Search::label('仓库名称')->text('warehouse_name', '仓库名称'),
            ])
            ->withApiRoute('warehouse')
            ->withActions([
                Actions::create()
            ])
            ->selectionChange()
            ->render();
    }

    protected function form()
    {
        return Factory::create('warehouse');
    }
}