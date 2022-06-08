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

class PaymentSheetSource extends Migrator
{
    public function change()
    {
        $table = $this->table('payment_sheet_source', ['engine' => 'InnoDB', 'collation' => 'utf8mb4_general_ci', 'comment' => '付款单源单', 'id' => 'id', 'signed' => true, 'primary_key' => ['id']]);
        $table->addColumn('payment_sheet_id', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '付款单id',])
            ->addColumn('source_id', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '源单id',])
            ->addColumn('order_date', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '源单日期',])
            ->addColumn('type', 'string', ['limit' => 30, 'null' => false, 'default' => '', 'signed' => true, 'comment' => '源单类型',])
            ->addColumn('order_code', 'string', ['limit' => 30, 'null' => false, 'default' => '', 'signed' => true, 'comment' => '源单编号',])
            ->addColumn('amount', 'decimal', ['precision' => 8, 'scale' => 2, 'default' => 0, 'signed' => true, 'comment' => '源单金额',])
            ->addColumn('payment_amount', 'decimal', ['precision' => 8, 'scale' => 2, 'default' => 0, 'signed' => true, 'comment' => '源单金额',])
            ->addColumn('remark', 'string', ['limit' => 200, 'null' => false, 'default' => '', 'signed' => true, 'comment' => '备注',])
            ->addColumn('created_at', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '创建时间',])
            ->addColumn('updated_at', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '更新时间',])
            ->addColumn('deleted_at', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '软删除',])
            ->create();
        $this->createdCollectionInfo();
    }

    public function createdCollectionInfo()
    {

        $table = $this->table('payment_sheet_collection_info', ['engine' => 'InnoDB', 'collation' => 'utf8mb4_general_ci', 'comment' => '付款单收款信息', 'id' => 'id', 'signed' => true, 'primary_key' => ['id']]);
        $table->addColumn('payment_sheet_id', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '付款单',])->addColumn('type', 'string', ['limit' => 30, 'null' => false, 'default' => '', 'signed' => true, 'comment' => '源单类型',])
            ->addColumn('payment_code', 'string', ['limit' => 30, 'null' => false, 'default' => '', 'signed' => true, 'comment' => '付款编号',])
            ->addColumn('payment_amount', 'decimal', ['precision' => 8, 'scale' => 2, 'default' => 0, 'signed' => true, 'comment' => '付款金额',])
            ->addColumn('payment_type', 'string', ['limit' => 200, 'null' => false, 'default' => '', 'signed' => true, 'comment' => '付款方式',])
            ->addColumn('transaction_no', 'string', ['limit' => 200, 'null' => false, 'default' => '', 'signed' => true, 'comment' => '交易号',])
            ->addColumn('remark', 'string', ['limit' => 200, 'null' => false, 'default' => '', 'signed' => true, 'comment' => '备注',])
            ->addColumn('created_at', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '创建时间',])
            ->addColumn('updated_at', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '更新时间',])
            ->addColumn('deleted_at', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '软删除',])
            ->create();
    }
}
