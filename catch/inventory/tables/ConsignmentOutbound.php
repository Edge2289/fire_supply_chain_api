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
                HeaderItem::label('状态')->width(80)->prop('status_i'),
                HeaderItem::label('编号')->width(130)->prop('consignment_outbound_code'),
                HeaderItem::label('仓库')->prop('warehouse_name'),
                HeaderItem::label('客户')->prop('customer_name'),
                HeaderItem::label('总额')->prop('amount'),
                HeaderItem::label('出库日期')->prop('outbound_time'),
                HeaderItem::label('总数量')->prop('put_num'),
                HeaderItem::label('转销售数量')->prop('inventory_quantity'),
                HeaderItem::label('明细')->prop('detail'),
                HeaderItem::label('审核状态')->prop('audit_status_i'),
                HeaderItem::label('备注')->prop('remark'),
                HeaderItem::label('操作')->width(200)->actions([
                    Actions::normal('修改', 'primary', 'handleUpdates'),
                    Actions::normal("转销售", 'primary', "turnSales")->icon('el-icon-bangzhu'),
                ])
            ])
            ->withSearch([
                Search::label('产品名称')->text('product_name', '产品名称'),
                Search::label('仓库')->select("warehouse_id", "请选择仓库", app(Warehouse::class)->tableGetWarehouse()),
            ])
            ->withApiRoute('consignmentOutbound')
            ->withActions([
                Actions::normal("新增", "primary", "handleAdd", "el-icon-plus"),
                Actions::normal("审核", 'primary', "audit")->icon('el-icon-bangzhu'),
                Actions::normal("作废", 'primary', "cancel")->icon('el-icon-bangzhu'),
            ])
            ->selectionChange()
            ->render();
    }

    protected function form()
    {
//        return Factory::create('warehouse');
    }
}