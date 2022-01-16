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

class ProductRegistered extends Migrator
{
    /**
     * 产品注册证
     */
    public function change()
    {
        $table = $this->table('product_registered', ['engine' => 'Myisam', 'collation' => 'utf8mb4_general_ci', 'comment' => '厂家生产许可证', 'id' => 'id', 'signed' => true, 'primary_key' => ['id']]);
        $table->addColumn('product_id', 'integer', ['limit' => MysqlAdapter::INT_MEDIUM, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '产品id',])
            ->addColumn('distribution_agreement_url', 'string', ['limit' => 200, 'default' => "", 'signed' => true, 'comment' => '经销协议url',])
            ->addColumn('signing_date', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'default' => 0, 'signed' => true, 'comment' => '签约日期',])
            ->addColumn('end_time', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'default' => 0, 'signed' => true, 'comment' => '有效期',])
            ->addColumn('payment_days', 'integer', ['limit' => MysqlAdapter::INT_MEDIUM, 'null' => false, 'default' => 0, 'signed' => false, 'comment' => '账期 天数',])
            ->addColumn('transaction_type', 'string', ['limit' => 100, 'null' => false, 'default' => '', 'signed' => false, 'comment' => '交易类型',])
            ->addColumn('admission_lowest_price', 'decimal', ['limit' => MysqlAdapter::INT_MEDIUM, 'default' => '', 'signed' => true, 'comment' => '当地进院最低价',])
            ->addColumn('guide_price', 'decimal', ['limit' => MysqlAdapter::INT_MEDIUM, 'default' => '', 'signed' => false, 'comment' => '当地指导价',])
            ->addColumn('provincial_price', 'decimal', ['limit' => MysqlAdapter::INT_MEDIUM, 'default' => '', 'signed' => false, 'comment' => '当地省标价',])
            ->addColumn('local_price', 'decimal', ['limit' => MysqlAdapter::INT_MEDIUM, 'default' => '', 'signed' => false, 'comment' => '当地市标价',])
            ->addColumn('clinical_use_department', 'string', ['limit' => 300, 'default' => '', 'signed' => true, 'comment' => '产品临床使用科室',])
            ->addColumn('remark', 'string', ['limit' => 300, 'default' => '', 'signed' => true, 'comment' => '备注',])

            ->addColumn('created_at', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'default' => 0, 'signed' => true, 'comment' => '创建时间',])
            ->addColumn('updated_at', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'default' => 0, 'signed' => true, 'comment' => '更新时间',])
            ->addColumn('deleted_at', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'default' => 0, 'signed' => true, 'comment' => '软删除',])
            ->create();
    }
}
