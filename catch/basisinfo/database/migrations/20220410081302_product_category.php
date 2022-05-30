<?php

use think\migration\Migrator;
use think\migration\db\Column;
use Phinx\Db\Adapter\MysqlAdapter;

class ProductCategory extends Migrator
{
    public function change()
    {
        $table = $this->table('product_category', ['engine' => 'InnoDB', 'collation' => 'utf8mb4_general_ci', 'comment' => '产品类别', 'id' => 'id', 'signed' => true, 'primary_key' => ['id']]);
        $table->addColumn('name', 'string', ['limit' => 60, 'default' => 0, 'signed' => true, 'comment' => '名称',])
            ->addColumn('p_id', 'integer', ['limit' => MysqlAdapter::INT_MEDIUM, 'default' => 0, 'signed' => true, 'comment' => '上级id',])
            ->addColumn('sort', 'integer', ['limit' => MysqlAdapter::INT_MEDIUM, 'default' => 0, 'signed' => true, 'comment' => '权重',])
            ->addColumn('note', 'string', ['limit' => 60, 'default' => "", 'signed' => true, 'comment' => '备注',])
            ->addColumn('created_at', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '创建时间',])
            ->addColumn('updated_at', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '更新时间',])
            ->addColumn('deleted_at', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '软删除',])
            ->create();
    }
}
