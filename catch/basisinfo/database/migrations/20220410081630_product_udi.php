<?php

use think\migration\Migrator;
use think\migration\db\Column;
use Phinx\Db\Adapter\MysqlAdapter;

class ProductUdi extends Migrator
{
    public function change()
    {
        $table = $this->table('product_udi', ['engine' => 'InnoDB', 'collation' => 'utf8mb4_general_ci', 'comment' => 'UDI', 'id' => 'id', 'signed' => true, 'primary_key' => ['id']]);
        $table->addColumn('udi', 'string', ['limit' => 300, 'default' => '', 'signed' => true, 'comment' => 'UDI',])
            ->addColumn('product_name', 'string', ['limit' => 300, 'default' => '', 'signed' => true, 'comment' => '产品名称',])
            ->addColumn('item_number', 'string', ['limit' => 300, 'default' => '', 'signed' => true, 'comment' => '型号',])
            ->addColumn('manufacturer', 'string', ['limit' => 300, 'default' => '', 'signed' => true, 'comment' => '生产厂家',])
            ->addColumn('registered', 'string', ['limit' => 300, 'default' => '', 'signed' => true, 'comment' => '注册/备案号',])
            ->addColumn('created_at', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '创建时间',])
            ->addColumn('updated_at', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '更新时间',])
            ->addColumn('deleted_at', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '软删除',])
            ->create();
    }
}
