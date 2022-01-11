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

class SuppleInfo extends Migrator
{
    public function change()
    {
        $table = $this->table('supple_info', ['engine' => 'Myisam', 'collation' => 'utf8mb4_general_ci', 'comment' => '补充信息', 'id' => 'id', 'signed' => true, 'primary_key' => ['id']]);
        $table->addColumn('business_license_id', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '营业执照id',])
            ->addColumn('name', 'string', ['limit' => 60, 'null' => false, 'default' => "", 'signed' => true, 'comment' => '被授权人姓名',])
            ->addColumn('certid', 'string', ['limit' => 60, 'null' => false, 'default' => "", 'signed' => true, 'comment' => '证件号码',])
            ->addColumn('phone', 'string', ['limit' => 20, 'null' => false, 'default' => "", 'signed' => true, 'comment' => '手机号',])
            ->addColumn('email', 'string', ['limit' => 30, 'null' => false, 'default' => '', 'signed' => false, 'comment' => '邮箱',])
            ->addColumn('license_start_date', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '授权日期开始',])
            ->addColumn('license_end_date', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '授权日期结束',])
            ->addColumn('license_date_long', 'integer', ['limit' => MysqlAdapter::INT_TINY, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '是否长期 {0:否,1:是}',])

            ->addColumn('license_area', 'string', ['limit' => 500, 'default' => '', 'signed' => true, 'comment' => '授权区域',])
            ->addColumn('product_line', 'string', ['limit' => 500, 'default' => '', 'signed' => false, 'comment' => '产品线',])
            ->addColumn('other', 'string', ['limit' => 500, 'default' => '', 'signed' => false, 'comment' => '备注',])
            // 付款信息
            ->addColumn('invoice_head', 'string', ['limit' => 200, 'null' => false, 'default' => '', 'signed' => false, 'comment' => '发票抬头',])
            ->addColumn('invoice_no', 'string', ['limit' => 200, 'null' => false, 'default' => '', 'signed' => false, 'comment' => '税号',])
            ->addColumn('invoice_bank', 'string', ['limit' => 200, 'null' => false, 'default' => '', 'signed' => false, 'comment' => '开户银行',])
            ->addColumn('invoice_bank_no', 'string', ['limit' => 200, 'null' => false, 'default' => '', 'signed' => false, 'comment' => '银行账号',])
            ->addColumn('company_address', 'string', ['limit' => 200, 'default' => '', 'signed' => false, 'comment' => '企业地址',])
            ->addColumn('company_phone', 'string', ['limit' => 100, 'default' => '', 'signed' => false, 'comment' => '企业电话',])

            ->addColumn('created_at', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '创建时间',])
            ->addColumn('updated_at', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '更新时间',])
            ->addColumn('deleted_at', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '软删除',])
            ->create();
    }
}
