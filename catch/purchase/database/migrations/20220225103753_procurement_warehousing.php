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
            ->addColumn('supplier_id', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'default' => 0, 'signed' => true, 'comment' => '供应商id',])
            ->addColumn('put_date', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'default' => 0, 'signed' => false, 'comment' => '入库时间',])
            ->addColumn('warehouse_id', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'default' => 0, 'signed' => true, 'comment' => '仓库id',])
            ->addColumn('put_num', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '入库数量',])
            ->addColumn('inspection_date', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'default' => 0, 'signed' => true, 'comment' => '收/验货日期',])
            ->addColumn('inspection_user_id', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'default' => 0, 'signed' => true, 'comment' => '收/验货人员',])
            ->addColumn('is_qualified', 'integer', ['limit' => MysqlAdapter::INT_TINY, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '是否合格{1:合格,2:不合格}',])
            ->addColumn('logistics_info', 'integer', ['limit' => MysqlAdapter::INT_TINY, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '物流信息{1:快递,2:送货,3:自提}',])
            ->addColumn('courier_company', 'string', ['limit' => 100, 'null' => false, 'default' => "", 'signed' => false, 'comment' => '快递公司',])
            ->addColumn('courier_code', 'string', ['limit' => 100, 'null' => false, 'default' => "", 'signed' => false, 'comment' => '快递单号',])
            ->addColumn('contact_name', 'string', ['limit' => 100, 'null' => false, 'default' => "", 'signed' => false, 'comment' => '联系人',])
            ->addColumn('phone', 'string', ['limit' => 100, 'null' => false, 'default' => "", 'signed' => false, 'comment' => '电话',])
            ->addColumn('attachment', 'string', ['limit' => 100, 'null' => false, 'default' => "", 'signed' => false, 'comment' => '供应商附件',])

            // 结算状态
            ->addColumn('amount', 'decimal', ['precision' => 8, 'scale' => 2, 'default' => 0, 'signed' => true, 'comment' => '金额',])
            ->addColumn('settlement_amount', 'decimal', ['precision' => 8, 'scale' => 2, 'default' => 0, 'signed' => true, 'comment' => '结算价格',])
            ->addColumn('settlement_status', 'integer', ['limit' => MysqlAdapter::INT_TINY, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '结算状态{0:未结,1:部分结算,2:已结算}',])
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
