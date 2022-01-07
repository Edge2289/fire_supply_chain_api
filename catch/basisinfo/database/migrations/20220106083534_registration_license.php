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

class RegistrationLicense extends Migrator
{
    public function change()
    {
        $table = $this->table('registration_license', ['engine' => 'Myisam', 'collation' => 'utf8mb4_general_ci', 'comment' => '备案凭证', 'id' => 'id', 'signed' => true, 'primary_key' => ['id']]);
        $table->addColumn('business_license_id', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '营业执照id',])
            ->addColumn('registration_license_code', 'string', ['limit' => 1000, 'null' => false, 'default' => "", 'signed' => true, 'comment' => '备案号',])
            ->addColumn('registration_date', 'string', ['limit' => 1000, 'null' => false, 'default' => "", 'signed' => true, 'comment' => '备案日期',])
            ->addColumn('operation_mode', 'string', ['limit' => 100, 'null' => false, 'default' => '1', 'signed' => false, 'comment' => '经营方式 {1:批发,2::零售.3:批发兼零售}',])
            ->addColumn('legal_person', 'string', ['limit' => 1000, 'null' => false, 'default' => '', 'signed' => false, 'comment' => '法人',])
            ->addColumn('incharge_person', 'string', ['limit' => 1000, 'null' => false, 'default' => "", 'signed' => true, 'comment' => '企业负责人',])
            ->addColumn('premise', 'string', ['limit' => 1000, 'null' => false, 'default' => '', 'signed' => false, 'comment' => '住所',])
            ->addColumn('residence', 'string', ['limit' => 1000, 'null' => false, 'default' => '', 'signed' => false, 'comment' => '经营场所',])
            ->addColumn('warehouse_address', 'string', ['limit' => 1000, 'null' => false, 'default' => '', 'signed' => false, 'comment' => '库房地址',])
            ->addColumn('equipment_class', 'string', ['limit' => 1000, 'null' => false, 'default' => '', 'signed' => false, 'comment' => '医疗分类 逗号分割',])


            ->addColumn('created_at', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '创建时间',])
            ->addColumn('updated_at', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '更新时间',])
            ->addColumn('deleted_at', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '软删除',])
            ->create();
    }
}
