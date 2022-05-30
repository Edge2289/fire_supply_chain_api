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

class OperatingLicense extends Migrator
{
    public function change()
    {
        $table = $this->table('operating_license', ['engine' => 'InnoDB', 'collation' => 'utf8mb4_general_ci', 'comment' => '经营许可证', 'id' => 'id', 'signed' => true, 'primary_key' => ['id']]);
        $table->addColumn('business_license_id', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '营业执照id',])
            ->addColumn('operating_license_code', 'string', ['limit' => 100, 'null' => false, 'default' => '', 'signed' => false, 'comment' => '许可证编号',])
            ->addColumn('operation_mode', 'integer', ['limit' => MysqlAdapter::INT_TINY, 'null' => false, 'default' => '1', 'signed' => false, 'comment' => '经营方式 {1:批发,2::零售.3:批发兼零售}',])
            ->addColumn('company_name', 'string', ['limit' => 100, 'null' => false, 'default' => '', 'signed' => false, 'comment' => '企业名称',])
            ->addColumn('legal_person', 'string', ['limit' => 60, 'null' => false, 'default' => '', 'signed' => false, 'comment' => '法人',])
            ->addColumn('incharge_person', 'string', ['limit' => 60, 'null' => false, 'default' => "", 'signed' => true, 'comment' => '企业负责人',])
            ->addColumn('business_start_date', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '有效期开始',])
            ->addColumn('business_end_date', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '有效期结束',])
            ->addColumn('premise', 'string', ['limit' => 300, 'null' => false, 'default' => '', 'signed' => false, 'comment' => '住所',])
            ->addColumn('residence', 'string', ['limit' => 300, 'null' => false, 'default' => '', 'signed' => false, 'comment' => '经营场所',])
            ->addColumn('warehouse_address', 'string', ['limit' => 300, 'null' => false, 'default' => '', 'signed' => false, 'comment' => '库房地址',])
            ->addColumn('equipment_class', 'string', ['limit' => 300, 'null' => false, 'default' => '', 'signed' => false, 'comment' => '医疗分类',])
            ->addColumn('created_at', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '创建时间',])
            ->addColumn('updated_at', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '更新时间',])
            ->addColumn('deleted_at', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '软删除',])
            ->create();
    }
}
