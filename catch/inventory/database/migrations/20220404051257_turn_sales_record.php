<?php

use think\migration\Migrator;
use think\migration\db\Column;
use Phinx\Db\Adapter\MysqlAdapter;

class TurnSalesRecord extends Migrator
{
    public function change()
    {
        $table = $this->table('turn_sales_record', ['engine' => 'InnoDB', 'collation' => 'utf8mb4_general_ci', 'comment' => '转销售记录表', 'id' => 'id', 'signed' => true, 'primary_key' => ['id']]);
        $table->addColumn('sales_order_id', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'default' => 0, 'signed' => true, 'comment' => '销售订单id',])
            ->addColumn('form_id', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'signed' => false, 'comment' => '来源id',])
            ->addColumn('warehouse_id', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'signed' => false, 'comment' => '仓库id',])
            ->addColumn('form_details_id', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'default' => 0, 'signed' => true, 'comment' => '来源商品详情id',])
            ->addColumn('form_type', 'integer', ['limit' => MysqlAdapter::INT_TINY, 'default' => 1, 'signed' => false, 'comment' => '来源类型{1:寄售出库,2:备货出库}',])
            ->addColumn('inventory_id', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'default' => 0, 'signed' => false, 'comment' => '库存id',])
            ->addColumn('inventory_batch_id', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'default' => 0, 'signed' => false, 'comment' => '库存批次',])
            ->addColumn('product_id', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'default' => 0, 'signed' => false, 'comment' => '产品id',])
            ->addColumn('product_sku_id', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'default' => 0, 'signed' => false, 'comment' => 'sku_id',])
            ->addColumn('quantity', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'default' => 0, 'signed' => false, 'comment' => '数量',])
            ->addColumn('created_at', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '创建时间',])
            ->addColumn('updated_at', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '更新时间',])
            ->addColumn('deleted_at', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '软删除',])
            ->create();
    }
}
