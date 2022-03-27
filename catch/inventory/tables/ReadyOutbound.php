<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/3/27
 * Time: 21:39
 */

namespace catchAdmin\inventory\tables;


use catchAdmin\inventory\model\Warehouse;
use catcher\CatchTable;
use catcher\library\table\Actions;
use catcher\library\table\HeaderItem;
use catcher\library\table\Search;

/**
 * Class ReadyOutbound
 * @package catchAdmin\inventory\tables
 */
class ReadyOutbound extends CatchTable
{
    protected function table()
    {
        return $this->getTable('readyOutbound')
            ->header([
                HeaderItem::label()->selection(),
                HeaderItem::label('编号')->width(80)->prop('id'),
                HeaderItem::label('产品名称')->prop('product_name'),
                HeaderItem::label('产品sku编号')->prop('product_sku_name'),
                HeaderItem::label('仓库')->prop('warehouse_name'),
                HeaderItem::label('供应商')->width(200)->prop('supplier_name'),
                HeaderItem::label('厂家')->prop('factory_name'),
                HeaderItem::label('数量')->prop('number'),
                HeaderItem::label('使用数量')->prop('use_number'),
                HeaderItem::label('操作')->width(120)->actions([
                    Actions::normal('查看批次', 'primary', 'view_batch')
                ])
            ])
            ->withSearch([
                Search::label('产品名称')->text('product_name', '产品名称'),
                Search::label('仓库')->select("warehouse_id", "请选择仓库", app(Warehouse::class)->tableGetWarehouse()),
            ])
            ->withApiRoute('readyOutbound')
            ->withActions([
//                Actions::create()
            ])
            ->selectionChange()
            ->render();
    }

    protected function form()
    {
//        return Factory::create('warehouse');
    }
}