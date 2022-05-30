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

class ProductSku extends Migrator
{
    /**
     * 产品sku
     */
    public function change()
    {
        $table = $this->table('product_sku', ['engine' => 'InnoDB', 'collation' => 'utf8mb4_general_ci', 'comment' => '产品sku', 'id' => 'id', 'signed' => true, 'primary_key' => ['id']]);
        $table->addColumn('product_id', 'integer', ['limit' => MysqlAdapter::INT_MEDIUM, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '产品id',])
            ->addColumn('product_code', 'string', ['limit' => 200, 'null' => false, 'default' => "", 'signed' => true, 'comment' => '产品编号',])
            ->addColumn('udi', 'string', ['limit' => 60, 'null' => false, 'default' => '', 'signed' => false, 'comment' => 'udi',])
            ->addColumn('sku_code', 'string', ['limit' => 100, 'null' => false, 'default' => 0, 'signed' => false, 'comment' => '规格',])
            ->addColumn('item_number', 'string', ['limit' => 100, 'null' => false, 'default' => '', 'signed' => false, 'comment' => '型号',])
            ->addColumn('unit_price_1', 'decimal', ['precision' => 8, 'scale' => 2, 'default' => 0, 'null' => false, 'signed' => true, 'comment' => '单价1',])
            ->addColumn('unit_price_2', 'decimal', ['precision' => 8, 'scale' => 2, 'default' => 0, 'null' => false, 'signed' => true, 'comment' => '单价2',])
            ->addColumn('unit_price_3', 'decimal', ['precision' => 8, 'scale' => 2, 'default' => 0, 'null' => false, 'signed' => true, 'comment' => '单价3',])
            ->addColumn('unit_price_4', 'decimal', ['precision' => 8, 'scale' => 2, 'default' => 0, 'null' => false, 'signed' => true, 'comment' => '单价4',])
            ->addColumn('procurement_price_1', 'decimal', ['precision' => 8, 'scale' => 2, 'default' => 0, 'null' => false, 'signed' => false, 'comment' => '采购价1',])
            ->addColumn('procurement_price_2', 'decimal', ['precision' => 8, 'scale' => 2, 'default' => 0, 'null' => false, 'signed' => false, 'comment' => '采购价2',])
            ->addColumn('validity_num', 'string', ['limit' => 60, 'default' => "", 'signed' => false, 'comment' => '有效期数',])
            ->addColumn('validity_type', 'string', ['limit' => 60, 'default' => "", 'signed' => true, 'comment' => '有效期类型',])
            ->addColumn('note', 'string', ['limit' => 100, 'default' => "", 'signed' => true, 'comment' => '备注',])
            ->addColumn('created_at', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'default' => 0, 'signed' => true, 'comment' => '创建时间',])
            ->addColumn('updated_at', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'default' => 0, 'signed' => true, 'comment' => '更新时间',])
            ->addColumn('deleted_at', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'default' => 0, 'signed' => true, 'comment' => '软删除',])
            ->create();
    }
}
