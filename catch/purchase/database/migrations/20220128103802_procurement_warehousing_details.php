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

class ProcurementWarehousingDetails extends Migrator
{
    public function change()
    {
        $table = $this->table('procurement_warehousing_details', ['engine' => 'Myisam', 'collation' => 'utf8mb4_general_ci', 'comment' => '入库详情单', 'id' => 'id', 'signed' => true, 'primary_key' => ['id']]);
        $table->addColumn('procurement_warehousing_id', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'default' => 0, 'signed' => true, 'comment' => '入库单id',])
            ->addColumn('product_id', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'default' => 0, 'signed' => false, 'comment' => '产品id',])

            // 数量 - 金额 (总金额-退款金额)
            ->addColumn('batch_number', 'string', ['limit' => 100, 'null' => false, 'default' => "", 'signed' => false, 'comment' => '批号',])
            ->addColumn('remark', 'string', ['limit' => 100, 'null' => false, 'default' => "", 'signed' => false, 'comment' => '备注',])

            // 审核信息
            ->addColumn('created_at', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '创建时间',])
            ->addColumn('updated_at', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '更新时间',])
            ->addColumn('deleted_at', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '软删除',])
            ->create();
    }
}
