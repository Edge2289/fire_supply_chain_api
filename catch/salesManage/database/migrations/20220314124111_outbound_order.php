<?php

use think\migration\Migrator;
use think\migration\db\Column;
use Phinx\Db\Adapter\MysqlAdapter;

class OutboundOrder extends Migrator
{
    public function change()
    {
        $table = $this->table('outbound_order', ['engine' => 'InnoDB', 'collation' => 'utf8mb4_general_ci', 'comment' => '出库单', 'id' => 'id', 'signed' => true, 'primary_key' => ['id']]);
        $table->addColumn('outbound_order_code', 'string', ['limit' => 60, 'default' => 0, 'signed' => true, 'comment' => '出库订单编号',])
            ->addColumn('sales_order_id', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'default' => 0, 'signed' => true, 'comment' => '销售订单id',])
            ->addColumn('company_id', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'default' => 0, 'signed' => true, 'comment' => '公司id',])
            ->addColumn('warehouse_id', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'default' => 0, 'signed' => true, 'comment' => '仓库id',])
            ->addColumn('outbound_time', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'signed' => false, 'comment' => '出库日期',])
            ->addColumn('outbound_man_id', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'default' => 0, 'signed' => true, 'comment' => '出库人员',])
            ->addColumn('supplier_id', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'default' => 0, 'signed' => false, 'comment' => '供应商',])
            ->addColumn('customer_info_id', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'default' => 0, 'signed' => false, 'comment' => '客户',])
            ->addColumn('logistics_code', 'string', ['limit' => 60, 'default' => 0, 'signed' => true, 'comment' => '物流公司',])
            ->addColumn('logistics_number', 'string', ['limit' => 60, 'default' => 0, 'signed' => true, 'comment' => '物流单号',])
            ->addColumn('outbound_num', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '出库总数量',])
            ->addColumn('amount', 'decimal', ['precision' => 8, 'scale' => 2, 'default' => 0, 'signed' => true, 'comment' => '价格总额',])
            ->addColumn('status', 'integer', ['limit' => MysqlAdapter::INT_TINY, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '状态{0:未完成,1:已完成,2:作废}',])
            ->addColumn('invoice_status', 'integer', ['limit' => MysqlAdapter::INT_TINY, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '收票状态{0:未收票,1:已收票}',])
            ->addColumn('remark', 'string', ['limit' => 100, 'null' => false, 'default' => "", 'signed' => false, 'comment' => '备注',])

            // 结算状态
            ->addColumn('settlement_amount', 'decimal', ['precision' => 8, 'scale' => 2, 'default' => 0, 'signed' => true, 'comment' => '结算价格',])
            ->addColumn('settlement_status', 'integer', ['limit' => MysqlAdapter::INT_TINY, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '结算状态{0:未结,1:已结}',])
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
