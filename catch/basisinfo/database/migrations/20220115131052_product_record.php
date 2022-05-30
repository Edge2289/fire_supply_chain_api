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

class ProductRecord extends Migrator
{
    /**
     * 产品备案
     */
    public function change()
    {
        $table = $this->table('product_record', ['engine' => 'InnoDB', 'collation' => 'utf8mb4_general_ci', 'comment' => '产品备案', 'id' => 'id', 'signed' => true, 'primary_key' => ['id']]);
        $table->addColumn('product_id', 'integer', ['limit' => MysqlAdapter::INT_MEDIUM, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '产品id',])
            ->addColumn('record_product_categories', 'integer', ['limit' => MysqlAdapter::INT_MEDIUM, 'null' => false, 'default' => 0, 'signed' => false, 'comment' => '产品分类',])
            ->addColumn('record_code', 'string', ['limit' => 100, 'null' => false, 'default' => '', 'signed' => false, 'comment' => '备案号',])
            ->addColumn('recorder_org_code', 'string', ['limit' => 300, 'default' => 0, 'signed' => true, 'comment' => '备案人组织机构代码',])
            ->addColumn('record_name', 'string', ['limit' => 60, 'default' => '', 'null' => false, 'signed' => false, 'comment' => '备案人名称',])
            ->addColumn('record_creator_company_address', 'string', ['limit' => 300, 'default' => '', 'signed' => false, 'comment' => '备案人生产地址',])
            ->addColumn('record_proxy_name', 'string', ['limit' => 100, 'default' => '', 'signed' => false, 'comment' => '代理人',])
            ->addColumn('record_proxy_address', 'string', ['limit' => 300, 'default' => '', 'signed' => false, 'comment' => '代理人注册住址',])
            ->addColumn('product_desc', 'string', ['limit' => 300, 'default' => '', 'signed' => false, 'comment' => '产品描述',])
            ->addColumn('preliminary_use', 'string', ['limit' => 300, 'default' => '', 'signed' => true, 'comment' => '预备用途',])
            ->addColumn('record_department', 'string', ['limit' => 300, 'default' => 0, 'signed' => true, 'comment' => '备案单位',])
            ->addColumn('record_time', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'default' => '', 'null' => false, 'signed' => true, 'comment' => '备案日期',])
            ->addColumn('record_remark', 'string', ['limit' => 300, 'default' => '', 'signed' => true, 'comment' => '备注',])
            ->addColumn('created_at', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'default' => 0, 'signed' => true, 'comment' => '创建时间',])
            ->addColumn('updated_at', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'default' => 0, 'signed' => true, 'comment' => '更新时间',])
            ->addColumn('deleted_at', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'default' => 0, 'signed' => true, 'comment' => '软删除',])
            ->create();
    }
}
