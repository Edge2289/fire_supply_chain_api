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

class ProcurementWarehousing extends Migrator
{
    public function change()
    {
        $table = $this->table('procurement_warehousing', ['engine' => 'InnoDB', 'collation' => 'utf8mb4_general_ci', 'comment' => '入库单', 'id' => 'id', 'signed' => true, 'primary_key' => ['id']]);
        $table->addColumn('purchase_order_id', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'default' => 0, 'signed' => true, 'comment' => '采购id',])
            ->addColumn('warehouse_entry_code', 'string', ['limit' => 30, 'null' => false, 'default' => "", 'signed' => true, 'comment' => '入库编号',])
            ->addColumn('put_user_id', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'default' => 0, 'signed' => true, 'comment' => '入库人员',])
            ->addColumn('put_date', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'default' => 0, 'signed' => false, 'comment' => '入库时间',])
            ->addColumn('warehouse_id', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'default' => 0, 'signed' => true, 'comment' => '仓库id',])
            ->addColumn('delivery_code', 'string', ['limit' => 100, 'null' => false, 'default' => "", 'signed' => false, 'comment' => '收货单号',])
            ->addColumn('put_num', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '入库数量',])
            ->addColumn('status', 'integer', ['limit' => MysqlAdapter::INT_TINY, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '状态{0:未完成,1:已完成,2:作废}',])
            ->addColumn('remark', 'string', ['limit' => 100, 'null' => false, 'default' => "", 'signed' => false, 'comment' => '备注',])
            ->addColumn('audit_status', 'integer', ['limit' => MysqlAdapter::INT_TINY, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '审核状态 {0:未审核,1:已审核,2:审核失败}',])
            ->addColumn('audit_info', 'string', ['limit' => 300, 'null' => false, 'default' => '', 'signed' => true, 'comment' => '审核信息',])
            ->addColumn('audit_user_id', 'integer', ['limit' => MysqlAdapter::INT_MEDIUM, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '审核人id',])
            ->addColumn('audit_user_name', 'string', ['limit' => 60, 'null' => false, 'default' => '', 'signed' => true, 'comment' => '审核人名字',])
            ->addColumn('created_at', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '创建时间',])
            ->addColumn('updated_at', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '更新时间',])
            ->addColumn('deleted_at', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '软删除',])
            ->create();
    }
}
