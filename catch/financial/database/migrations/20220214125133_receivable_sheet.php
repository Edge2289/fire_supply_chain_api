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
use Phinx\Db\Adapter\MysqlAdapter;

class ReceivableSheet extends Migrator
{
    public function change()
    {
        $table = $this->table('receivable_sheet', ['engine' => 'Myisam', 'collation' => 'utf8mb4_general_ci', 'comment' => '回款单', 'id' => 'id', 'signed' => true, 'primary_key' => ['id']]);
        $table->addColumn('receivable_code', 'string', ['limit' => 60, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '回款单号',])
            ->addColumn('receivable_time', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '回款时间',])
            ->addColumn('amount', 'decimal', ['precision' => 8, 'scale' => 2, 'default' => 0, 'signed' => true, 'comment' => '回款金额',])
            ->addColumn('payment_type', 'integer', ['limit' => MysqlAdapter::INT_MEDIUM, 'null' => false, 'default' => 1, 'signed' => true, 'comment' => '回款类型{1:常规,2:预收款,3:尾款,4:保证金,5:其他}',])
            ->addColumn('payment_method', 'integer', ['limit' => MysqlAdapter::INT_MEDIUM, 'null' => false, 'default' => 1, 'signed' => true, 'comment' => '支付方式{1:银行转账,2:现金,3:其他}',])
            ->addColumn('attachment', 'string', ['limit' => 500, 'default' => '', 'signed' => false, 'comment' => '附件',])
            ->addColumn('other', 'string', ['limit' => 500, 'default' => '', 'signed' => false, 'comment' => '备注',])
            ->addColumn('created_user_id', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '创建人',])
            ->addColumn('audit_status', 'integer', ['limit' => MysqlAdapter::INT_TINY, 'default' => 0, 'signed' => true, 'comment' => '审核状态 {0:未审核,1:已审核,2:审核失败}',])
            ->addColumn('audit_info', 'string', ['limit' => 300, 'default' => '', 'signed' => true, 'comment' => '审核信息',])
            ->addColumn('audit_user_id', 'integer', ['limit' => MysqlAdapter::INT_MEDIUM, 'default' => 0, 'signed' => true, 'comment' => '审核人id',])
            ->addColumn('audit_user_name', 'string', ['limit' => 60, 'default' => '', 'signed' => true, 'comment' => '审核人名字',])
            ->addColumn('created_at', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '创建时间',])
            ->addColumn('updated_at', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '更新时间',])
            ->addColumn('deleted_at', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '软删除',])
            ->create();
    }
}
