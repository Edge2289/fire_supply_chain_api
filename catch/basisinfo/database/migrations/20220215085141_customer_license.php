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

class CustomerLicense extends Migrator
{
    public function change()
    {
        $table = $this->table('customer_license', ['engine' => 'InnoDB', 'collation' => 'utf8mb4_general_ci', 'comment' => '客户资质表', 'id' => 'id', 'signed' => true, 'primary_key' => ['id']]);
        $table->addColumn('customer_info_id', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '客户信息id',])
            ->addColumn('company_name', 'string', ['limit' => 300, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '企业名称',])
            ->addColumn('foreign_company', 'string', ['limit' => 300, 'default' => "", 'signed' => false, 'comment' => '国外注册公司',])
            ->addColumn('company_type', 'string', ['limit' => 300, 'default' => '', 'signed' => false, 'comment' => '企业类型',])
            ->addColumn('unified_code', 'string', ['limit' => 100, 'null' => false, 'default' => '', 'signed' => false, 'comment' => '统一社会信用代码',])
            ->addColumn('residence', 'string', ['limit' => 300, 'null' => false, 'default' => '', 'signed' => false, 'comment' => '住所',])
            ->addColumn('legal_person', 'string', ['limit' => 60, 'null' => false, 'default' => '', 'signed' => false, 'comment' => '法人',])
            ->addColumn('data_maintenance', 'string', ['limit' => 60, 'null' => false, 'default' => "", 'signed' => false, 'comment' => '资料维护 1 经营许可证 2 经营备案凭证',])
            ->addColumn('registration_date', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'default' => 0, 'signed' => true, 'comment' => '成立日期',])
            ->addColumn('registered_capital', 'string', ['limit' => 100, 'default' => '', 'signed' => false, 'comment' => '注册资本',])
            ->addColumn('business_start_date', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '营业期限开始',])
            ->addColumn('business_end_date', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '营业期限结束',])
            ->addColumn('business_date_long', 'integer', ['limit' => MysqlAdapter::INT_TINY, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '是否长期 {0:否,1:是}',])
            ->addColumn('establish_date', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '登记日期',])
            ->addColumn('business_scope', 'string', ['limit' => 300, 'null' => false, 'default' => '', 'signed' => false, 'comment' => '经营范围',])
            ->addColumn('other', 'string', ['limit' => 300, 'default' => '', 'signed' => false, 'comment' => '备注',])
            ->addColumn('created_at', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '创建时间',])
            ->addColumn('updated_at', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '更新时间',])
            ->addColumn('deleted_at', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '软删除',])
            ->create();
    }
}
