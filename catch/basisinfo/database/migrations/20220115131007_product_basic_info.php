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

class ProductBasicInfo extends Migrator
{
    /**
     * 产品基础信息
     */
    public function change()
    {
        $table = $this->table('product_distribution_info', ['engine' => 'Myisam', 'collation' => 'utf8mb4_general_ci', 'comment' => '产品基础信息', 'id' => 'id', 'signed' => true, 'primary_key' => ['id']]);
        $table->addColumn('product_name', 'string', ['limit' => 300, 'null' => false, 'default' => '', 'signed' => true, 'comment' => '产品名称',])
            ->addColumn('product_img', 'string', ['limit' => 200, 'default' => '', 'signed' => false, 'comment' => '产品实物图片',])
            ->addColumn('storage_conditions', 'string', ['limit' => 100, 'default' => '', 'signed' => true, 'comment' => '储运条件',])
            ->addColumn('data_maintenance', 'string', ['limit' => 30, 'default' => "", 'signed' => false, 'comment' => '资料维护 1 注册证 2 备案凭证',])
            ->addColumn('audit_status', 'integer', ['limit' => MysqlAdapter::INT_TINY, 'default' => 0, 'signed' => true, 'comment' => '审核状态 {0:未审核,1:已审核,2:审核失败}',])
            ->addColumn('audit_info', 'string', ['limit' => 300, 'default' => '', 'signed' => true, 'comment' => '审核信息',])
            ->addColumn('audit_user_id', 'integer', ['limit' => MysqlAdapter::INT_MEDIUM, 'default' => 0, 'signed' => true, 'comment' => '审核人id',])
            ->addColumn('audit_user_name', 'string', ['limit' => 60, 'default' => '', 'signed' => true, 'comment' => '审核人名字',])
            ->addColumn('created_at', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'default' => 0, 'signed' => true, 'comment' => '创建时间',])
            ->addColumn('updated_at', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'default' => 0, 'signed' => true, 'comment' => '更新时间',])
            ->addColumn('deleted_at', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'default' => 0, 'signed' => true, 'comment' => '软删除',])
            ->create();
    }
}
