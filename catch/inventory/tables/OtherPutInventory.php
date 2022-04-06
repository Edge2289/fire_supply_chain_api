<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/4/5
 * Time: 21:18
 */

namespace catchAdmin\inventory\tables;


use catchAdmin\inventory\model\Warehouse;
use catcher\CatchTable;
use catcher\library\table\Actions;
use catcher\library\table\HeaderItem;
use catcher\library\table\Search;

/**
 * Class OtherPutInventory
 * @package catchAdmin\inventory\tables
 */
class OtherPutInventory extends CatchTable
{
    protected function table()
    {
        return $this->getTable('otherPutInventory')
            ->header([
                HeaderItem::label()->selection(),
                HeaderItem::label('状态')->width(80)->prop('status_i'),
                HeaderItem::label('编号')->width(130)->prop('other_put_inventory_code'),
                HeaderItem::label('仓库')->prop('warehouse_name'),
                HeaderItem::label('总额')->prop('amount'),
                HeaderItem::label('出库日期')->prop('outbound_time'),
                HeaderItem::label('总数量')->prop('put_num'),
                HeaderItem::label('明细')->prop('detail'),
                HeaderItem::label('审核状态')->prop('audit_status_i'),
                HeaderItem::label('备注')->prop('remark'),
                HeaderItem::label('操作')->width(100)->actions([
                    Actions::normal('修改', 'primary', 'handleUpdates'),
                ])
            ])
            ->withSearch([
                Search::label('产品名称')->text('product_name', '产品名称'),
                Search::label('仓库')->select("warehouse_id", "请选择仓库", app(Warehouse::class)->tableGetWarehouse()),
            ])
            ->withApiRoute('otherPutInventory')
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
        // TODO: Implement form() method.
    }
}