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

class ProductQualification extends Migrator
{
    /**
     * 产品经销商信息
     */
    public function change()
    {
        $table = $this->table('product_qualification', ['engine' => 'InnoDB', 'collation' => 'utf8mb4_general_ci', 'comment' => '产品经销商信息', 'id' => 'id', 'signed' => true, 'primary_key' => ['id']]);
        $table->addColumn('product_id', 'integer', ['limit' => MysqlAdapter::INT_MEDIUM, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '产品id',])
            ->addColumn('product_quality_url', 'string', ['limit' => 200, 'default' => "", 'signed' => true, 'comment' => '产品质量标准',])
            ->addColumn('imported_documents_url', 'string', ['limit' => 200, 'default' => "", 'signed' => true, 'comment' => '进口产品相关证件',])
            ->addColumn('report_delivery_url', 'string', ['limit' => 200, 'default' => "", 'signed' => true, 'comment' => '出厂检验报告',])
            ->addColumn('product_appearance_url', 'string', ['limit' => 200, 'default' => "", 'signed' => true, 'comment' => '产品外观',])
            ->addColumn('product_register_url', 'string', ['limit' => 200, 'default' => "", 'signed' => true, 'comment' => '产品注册证',])
            ->addColumn('created_at', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'default' => 0, 'signed' => true, 'comment' => '创建时间',])
            ->addColumn('updated_at', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'default' => 0, 'signed' => true, 'comment' => '更新时间',])
            ->addColumn('deleted_at', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'default' => 0, 'signed' => true, 'comment' => '软删除',])
            ->create();
    }
}
