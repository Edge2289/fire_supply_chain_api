<?php

use think\migration\Migrator;
use think\migration\db\Column;
use Phinx\Db\Adapter\MysqlAdapter;

class OtherOutboundDetails extends Migrator
{
    public function change()
    {
        $table = $this->table('other_outbound_details', ['engine' => 'InnoDB', 'collation' => 'utf8mb4_general_ci', 'comment' => 'xxx', 'id' => 'id', 'signed' => true, 'primary_key' => ['id']]);
        $table->addColumn('other_outbound_id', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'default' => 0, 'signed' => true, 'comment' => '寄售出库id',])
            ->addColumn('inventory_id', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'default' => 0, 'signed' => false, 'comment' => '库存id',])
            ->addColumn('inventory_batch_id', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'default' => 0, 'signed' => false, 'comment' => '库存批次id',])
            ->addColumn('product_id', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'default' => 0, 'signed' => false, 'comment' => '产品id',])
            ->addColumn('product_sku_id', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'default' => 0, 'signed' => false, 'comment' => 'sku_id',])
            ->addColumn('product_code', 'string', ['limit' => 50, 'null' => false, 'default' => "", 'signed' => false, 'comment' => '产品编号',])
            ->addColumn('item_number', 'string', ['limit' => 50, 'null' => false, 'default' => "", 'signed' => false, 'comment' => '货号',])
            ->addColumn('sku_code', 'string', ['limit' => 50, 'null' => false, 'default' => "", 'signed' => false, 'comment' => 'sku',])
            ->addColumn('tax_rate', 'decimal', ['precision' => 8, 'scale' => 2, 'default' => 0, 'signed' => false, 'comment' => '税率',])
            ->addColumn('unit_price', 'decimal', ['precision' => 8, 'scale' => 2, 'default' => 0, 'signed' => false, 'comment' => '单价',])
            ->addColumn('amount', 'decimal', ['precision' => 8, 'scale' => 2, 'default' => 0, 'signed' => false, 'comment' => '总额',])
            ->addColumn('quantity', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'default' => 0, 'signed' => false, 'comment' => '总数',])
            ->addColumn('note', 'string', ['limit' => 60, 'null' => false, 'default' => "", 'signed' => false, 'comment' => '备注',])
            ->addColumn('created_at', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '创建时间',])
            ->addColumn('updated_at', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '更新时间',])
            ->addColumn('deleted_at', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '软删除',])
            ->create();
    }
}
