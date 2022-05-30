<?php

use think\migration\Migrator;
use think\migration\db\Column;
use Phinx\Db\Adapter\MysqlAdapter;

class ProductEntity extends Migrator
{
    public function change()
    {
        $table = $this->table('product_entity', ['engine' => 'InnoDB', 'collation' => 'utf8mb4_general_ci', 'comment' => '产品单位', 'id' => 'id', 'signed' => true, 'primary_key' => ['id']]);
        $table->addColumn('product_id', 'integer', ['limit' => MysqlAdapter::INT_MEDIUM, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '产品id',])
            ->addColumn('product_sku_id', 'integer', ['limit' => MysqlAdapter::INT_MEDIUM, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '产品sku_id',])
            ->addColumn('deputy_unit_name', 'string', ['limit' => 60, 'null' => false, 'default' => "", 'signed' => true, 'comment' => '单位名',])
            ->addColumn('proportion', 'integer', ['limit' => MysqlAdapter::INT_MEDIUM, 'null' => false, 'default' => 1, 'signed' => true, 'comment' => '比例',])
            ->addColumn('created_at', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '创建时间',])
            ->addColumn('updated_at', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '更新时间',])
            ->addColumn('deleted_at', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '软删除',])
            ->create();
    }
}
