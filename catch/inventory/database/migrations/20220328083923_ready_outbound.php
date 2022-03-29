<?php

use think\migration\Migrator;
use think\migration\db\Column;
use Phinx\Db\Adapter\MysqlAdapter;

class ReadyOutbound extends Migrator
{
    public function change()
    {
        $table = $this->table('xxx', ['engine' => 'InnoDB', 'collation' => 'utf8mb4_general_ci', 'comment' => 'xxx', 'id' => 'id', 'signed' => true, 'primary_key' => ['id']]);
        $table->addColumn('code', 'string', ['limit' => 60, 'default' => 0, 'signed' => true, 'comment' => '编号',])
            ->addColumn('created_at', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '创建时间',])
            ->addColumn('updated_at', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '更新时间',])
            ->addColumn('deleted_at', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '软删除',])
            ->create();
    }
}
