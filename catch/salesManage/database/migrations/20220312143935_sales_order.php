<?php

use think\migration\Migrator;
use think\migration\db\Column;
use Phinx\Db\Adapter\MysqlAdapter;

class SalesOrder extends Migrator
{
    public function change()
    {
        $table = $this->table('sales_order', ['engine' => 'InnoDB', 'collation' => 'utf8mb4_general_ci', 'comment' => '销售订单', 'id' => 'id', 'signed' => true, 'primary_key' => ['id']]);
        $table->addColumn('order_code', 'string', ['limit' => 60, 'default' => 0, 'signed' => true, 'comment' => '销售订单编号',])
            ->addColumn('customer_code', 'string', ['limit' => 60, 'default' => 0, 'signed' => true, 'comment' => '客户单号',])
            ->addColumn('company_id', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'default' => 0, 'signed' => true, 'comment' => '公司id',])
            ->addColumn('sales_time', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'signed' => false, 'comment' => '销售日期',])
            ->addColumn('salesman_id', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'default' => 0, 'signed' => true, 'comment' => '销售人员',])
            ->addColumn('supplier_id', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'default' => 0, 'signed' => false, 'comment' => '供应商',])
            ->addColumn('customer_info_id', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'default' => 0, 'signed' => false, 'comment' => '客户',])
            ->addColumn('num', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '商品总数量',])
            ->addColumn('put_num', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '已发货数量',])
            ->addColumn('amount', 'decimal', ['precision' => 8, 'scale' => 2, 'default' => 0, 'signed' => true, 'comment' => '价格总额',])
            ->addColumn('sales_type', 'integer', ['limit' => MysqlAdapter::INT_TINY, 'null' => false, 'default' => 1, 'signed' => true, 'comment' => '销售方式{1:正常销售,2:备案转销售,3:寄售转销售,4:经销商提交}',])
            ->addColumn('status', 'integer', ['limit' => MysqlAdapter::INT_TINY, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '状态{0:未完成,1:已完成,2:作废}',])
            ->addColumn('settlement_type', 'integer', ['limit' => MysqlAdapter::INT_TINY, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '结算类型{0:现结,1:月结}',])
            ->addColumn('settlement_status', 'integer', ['limit' => MysqlAdapter::INT_TINY, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '结算状态{0:未结,1:已结}',])
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
