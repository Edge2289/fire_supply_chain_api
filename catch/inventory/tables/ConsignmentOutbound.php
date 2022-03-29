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
 * Class ConsignmentOutbound
 * @package catchAdmin\inventory\tables
 */
class ConsignmentOutbound extends CatchTable
{
    protected function table()
    {
        return $this->getTable('consignmentOutbound')
            ->header([
                HeaderItem::label()->selection(),
                HeaderItem::label('编号')->width(80)->prop('consignment_outbound_code'),
                HeaderItem::label('产品名称')->prop('product_name'),
                HeaderItem::label('产品sku编号')->prop('product_sku_name'),
                HeaderItem::label('仓库')->prop('warehouse_name'),
                HeaderItem::label('供应商')->width(200)->prop('supplier_name'),
                HeaderItem::label('厂家')->prop('factory_name'),
                HeaderItem::label('总数量')->prop('put_num'),
                HeaderItem::label('转销售数量')->prop('resold_quantity'),
                HeaderItem::label('明细')->prop('details'),
                HeaderItem::label('操作')->width(120)->actions([
                    Actions::normal('修改', 'primary', 'handle_update')
                ])
            ])
            ->withSearch([
                Search::label('产品名称')->text('product_name', '产品名称'),
                Search::label('仓库')->select("warehouse_id", "请选择仓库", app(Warehouse::class)->tableGetWarehouse()),
            ])
            ->withApiRoute('consignmentOutbound')
            ->withActions([
                Actions::create(),
                Actions::normal("审核", 'primary', "audit")->icon('el-icon-bangzhu'),
                Actions::normal("备货转销售", 'primary', "turnSales")->icon('el-icon-bangzhu'),
            ])
            ->selectionChange()
            ->render();
    }

    protected function form()
    {
//        return Factory::create('warehouse');
    }
}