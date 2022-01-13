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

class FactoryProductionLicense extends Migrator
{
    public function change()
    {
        $table = $this->table('factory_production_license', ['engine' => 'Myisam', 'collation' => 'utf8mb4_general_ci', 'comment' => '厂家生产许可证', 'id' => 'id', 'signed' => true, 'primary_key' => ['id']]);
        $table->addColumn('factory_id', 'integer', ['limit' => MysqlAdapter::INT_MEDIUM, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '厂家id',])
            ->addColumn('production_license_url', 'string', ['limit' => 200, 'null' => false, 'default' => "", 'signed' => true, 'comment' => '生产许可证',])
            ->addColumn('license_code', 'string', ['limit' => 100, 'null' => false, 'default' => '', 'signed' => false, 'comment' => '许可证编号',])
            ->addColumn('business_start_date', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '营业期限开始',])
            ->addColumn('business_end_date', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '营业期限结束',])
            ->addColumn('company_name', 'string', ['limit' => 300, 'default' => 0, 'null' => false, 'signed' => true, 'comment' => '企业名称',])
            ->addColumn('legal_person', 'string', ['limit' => 60, 'default' => '', 'null' => false, 'signed' => false, 'comment' => '法人',])
            ->addColumn('head_name', 'string', ['limit' => 100, 'default' => '', 'null' => false, 'signed' => false, 'comment' => '企业负责人',])
            ->addColumn('production_address', 'string', ['limit' => 300, 'default' => '', 'null' => false, 'signed' => false, 'comment' => '生产地址',])
            ->addColumn('residence', 'string', ['limit' => 300, 'default' => '', 'null' => false, 'signed' => false, 'comment' => '住所',])
            ->addColumn('production_scope', 'string', ['limit' => 300, 'default' => '', 'null' => false, 'signed' => false, 'comment' => '生产范围',])
            ->addColumn('license_department', 'string', ['limit' => 300, 'default' => '', 'signed' => false, 'comment' => '发证部门',])
            ->addColumn('license_date', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'default' => 0, 'signed' => true, 'comment' => '发证日期',])
            ->addColumn('created_at', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'default' => 0, 'signed' => true, 'comment' => '创建时间',])
            ->addColumn('updated_at', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'default' => 0, 'signed' => true, 'comment' => '更新时间',])
            ->addColumn('deleted_at', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'default' => 0, 'signed' => true, 'comment' => '软删除',])
            ->create();
    }
}
