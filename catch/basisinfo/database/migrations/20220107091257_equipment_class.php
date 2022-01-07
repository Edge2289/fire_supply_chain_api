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

class EquipmentClass extends Migrator
{
    public function change()
    {
        $table = $this->table('equipment_class', ['engine' => 'Myisam', 'collation' => 'utf8mb4_general_ci', 'comment' => '备案凭证', 'id' => 'id', 'signed' => true, 'primary_key' => ['id']]);
        $table->addColumn('scope2017', 'string', ['limit' => 1000, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '2017 版医疗器械',])
            ->addColumn('scope2002', 'string', ['limit' => 1000, 'null' => false, 'default' => "", 'signed' => true, 'comment' => '2022 版医疗器械',])
            ->addColumn('created_at', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '创建时间',])
            ->addColumn('updated_at', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '更新时间',])
            ->addColumn('deleted_at', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '软删除',])
            ->create();
    }
}
