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
        $table = $this->table('product_registered', ['engine' => 'Myisam', 'collation' => 'utf8mb4_general_ci', 'comment' => '产品注册证', 'id' => 'id', 'signed' => true, 'primary_key' => ['id']]);
        $table->addColumn('product_id', 'integer', ['limit' => MysqlAdapter::INT_MEDIUM, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '产品id',])
            ->addColumn('registered_license_url', 'string', ['limit' => 200, 'null' => false, 'default' => "", 'signed' => true, 'comment' => ' 注册证',])
            ->addColumn('registered_product_categories', 'integer', ['limit' => MysqlAdapter::INT_MEDIUM, 'default' => 0, 'signed' => false, 'comment' => '产品分类',])
            ->addColumn('registered_code', 'string', ['limit' => 100, 'null' => false, 'default' => '', 'signed' => false, 'comment' => '注册证编号',])
            ->addColumn('registered_address', 'string', ['limit' => 300, 'default' => '', 'null' => false, 'signed' => true, 'comment' => '生产地址',])
            ->addColumn('registered_name', 'string', ['limit' => 60, 'default' => '', 'null' => false, 'signed' => false, 'comment' => '注册人名称',])
            ->addColumn('registered_company_address', 'string', ['limit' => 300, 'default' => '', 'null' => false, 'signed' => false, 'comment' => '注册人住所',])
            ->addColumn('record_proxy_name', 'string', ['limit' => 300, 'default' => '', 'null' => false, 'signed' => false, 'comment' => '代理人名称',])
            ->addColumn('registered_proxy_address', 'string', ['limit' => 300, 'default' => '', 'null' => false, 'signed' => false, 'comment' => '代理人住址',])
            ->addColumn('comprise_desc', 'string', ['limit' => 300, 'default' => '', 'null' => false, 'signed' => false, 'comment' => '结构及组成',])
            ->addColumn('product_desc', 'string', ['limit' => 300, 'default' => '', 'null' => false, 'signed' => true, 'comment' => '适用范围',])
            ->addColumn('registered_time', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '批准日期',])
            ->addColumn('end_time', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '有效期至',])
            ->addColumn('registered_department', 'string', ['limit' => 300, 'default' => '', 'signed' => true, 'comment' => '审批部门',])
            ->addColumn('registered_remark', 'string', ['limit' => 300, 'default' => '', 'signed' => true, 'comment' => '备注',])

            ->addColumn('created_at', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'default' => 0, 'signed' => true, 'comment' => '创建时间',])
            ->addColumn('updated_at', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'default' => 0, 'signed' => true, 'comment' => '更新时间',])
            ->addColumn('deleted_at', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'default' => 0, 'signed' => true, 'comment' => '软删除',])
            ->create();
    }
}
