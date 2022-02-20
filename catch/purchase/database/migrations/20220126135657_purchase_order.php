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
use \Phinx\Db\Adapter\MysqlAdapter;

class PurchaseOrder extends Migrator
{
    public function change()
    {
        $table = $this->table('purchase_order', ['engine' => 'Myisam', 'collation' => 'utf8mb4_general_ci', 'comment' => '营业执照表', 'id' => 'id', 'signed' => true, 'primary_key' => ['id']]);
        $table->addColumn('purchase_code', 'string', ['limit' => 30, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '采购编号',])
            ->addColumn('company_id', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'default' => 0, 'signed' => true, 'comment' => '公司id',])
            ->addColumn('purchase_date', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'signed' => false, 'comment' => '采购日期',])
            ->addColumn('user_id', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'default' => 0, 'signed' => true, 'comment' => '采购人员',])
            ->addColumn('supplier_id', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'default' => 0, 'signed' => false, 'comment' => '供应商',])

            // 数量 - 金额 (总金额-退款金额)
            ->addColumn('num', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '商品总数量',])
            ->addColumn('put_num', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '入库数量',])
            ->addColumn('amount', 'decimal', ['precision' => 8, 'scale' => 2, 'default' => 0, 'signed' => true, 'comment' => '价格',])

            // 状态
            ->addColumn('status', 'integer', ['limit' => MysqlAdapter::INT_TINY, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '状态{0:未完成,1:已完成,2:作废}',])
            ->addColumn('settlement_status', 'integer', ['limit' => MysqlAdapter::INT_TINY, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '结算类型{0:现结,1:月结}',])
            ->addColumn('invoice_status', 'integer', ['limit' => MysqlAdapter::INT_TINY, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '开票状态{0:未开票,1:已开票}',])
            ->addColumn('remark', 'string', ['limit' => 100, 'null' => false, 'default' => "", 'signed' => false, 'comment' => '备注',])

            // 审核信息
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
