<?php
// +----------------------------------------------------------------------
// | CatchAdmin [Just Like ～ ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017~{$year} http://catchadmin.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( https://github.com/yanwenwu/catch-admin/blob/master/LICENSE.txt )
// +----------------------------------------------------------------------
// | Author: JaguarJack [ njphper@gmail.com ]
// +----------------------------------------------------------------------

use think\migration\Migrator;
use think\migration\db\Column;
use Phinx\Db\Adapter\MysqlAdapter;

class Warehouse extends Migrator
{
    public function change()
    {
        $table = $this->table('warehouse', ['engine' => 'InnoDB', 'collation' => 'utf8mb4_general_ci', 'comment' => '仓库', 'id' => 'id', 'signed' => true, 'primary_key' => ['id']]);
        $table->addColumn('company_id', 'integer', ['limit' => MysqlAdapter::INT_MEDIUM, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '公司id',])
            ->addColumn('warehouse_name', 'string', ['limit' => 200, 'null' => false, 'default' => '', 'signed' => true, 'comment' => '仓库名称',])
            ->addColumn('warehouse_code', 'string', ['limit' => 100, 'null' => false, 'default' => '', 'signed' => false, 'comment' => '仓库编号',])
            ->addColumn('warehouse_type', 'integer', ['limit' => MysqlAdapter::INT_TINY, 'null' => false, 'default' => '1', 'signed' => false, 'comment' => '仓库类别 {1:待检库,2::合格库.3:不合格库,4:非医疗器械库}',])
            ->addColumn('address', 'string', ['limit' => 100, 'null' => false, 'default' => '', 'signed' => false, 'comment' => '地址',])
            ->addColumn('contact', 'string', ['limit' => 60, 'null' => false, 'default' => '', 'signed' => false, 'comment' => '联系人',])
            ->addColumn('contact_phone', 'string', ['limit' => 60, 'null' => false, 'default' => "", 'signed' => true, 'comment' => '联系电话',])
            ->addColumn('note', 'string', ['limit' => 300, 'null' => false, 'default' => '', 'signed' => false, 'comment' => '备注',])
            ->addColumn('created_at', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '创建时间',])
            ->addColumn('updated_at', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '更新时间',])
            ->addColumn('deleted_at', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '软删除',])
            ->create();
    }
}
