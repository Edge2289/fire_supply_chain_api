<?php

use think\migration\Migrator;
use think\migration\db\Column;
use Phinx\Db\Adapter\MysqlAdapter;

class Invoice extends Migrator
{
    public function change()
    {
        $table = $this->table('invoice_sheet', ['engine' => 'InnoDB', 'collation' => 'utf8mb4_general_ci', 'comment' => '发票', 'id' => 'id', 'signed' => true, 'primary_key' => ['id']]);
        $table->addColumn('invoice_code', 'string', ['limit' => 60, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '发票单号',])
            ->addColumn('invoice_number', 'string', ['limit' => 60, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '发票号码',])
            ->addColumn('order_type', 'integer', ['limit' => MysqlAdapter::INT_MEDIUM, 'null' => false, 'default' => 1, 'signed' => true, 'comment' => '订单类型{1:采购订单,2:出库订单}',])
            ->addColumn('invoice_man_id', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '成员',])
            ->addColumn('customer_info_id', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '客户id',])
            ->addColumn('invoice_time', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '发票时间',])
            ->addColumn('amount', 'decimal', ['precision' => 8, 'scale' => 2, 'default' => 0, 'signed' => true, 'comment' => '发票金额',])
            ->addColumn('invoice_type', 'integer', ['limit' => MysqlAdapter::INT_MEDIUM, 'null' => false, 'default' => 1, 'signed' => true, 'comment' => '发票类型{1:增值税普通发票,2:增值税专用发票}',])
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
