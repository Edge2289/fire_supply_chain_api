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
        $table = $this->table('product_sku', ['engine' => 'Myisam', 'collation' => 'utf8mb4_general_ci', 'comment' => '产品sku', 'id' => 'id', 'signed' => true, 'primary_key' => ['id']]);
        $table->addColumn('product_id', 'integer', ['limit' => MysqlAdapter::INT_MEDIUM, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '产品id',])
            ->addColumn('product_code', 'string', ['limit' => 200, 'null' => false, 'default' => "", 'signed' => true, 'comment' => '产品编号',])
            ->addColumn('sku_code', 'string', ['limit' => 100, 'null' => false, 'default' => 0, 'signed' => false, 'comment' => '规格型号',])
            ->addColumn('item_number', 'string', ['limit' => 100, 'null' => false, 'default' => '', 'signed' => false, 'comment' => '货号/sku',])
            ->addColumn('unit_price', 'decimal', ['precision' => 8, 'scale' => 2, 'default' => 0, 'null' => false, 'signed' => true, 'comment' => '单价',])
            ->addColumn('tax_rate', 'integer', ['limit' => MysqlAdapter::INT_MEDIUM, 'default' => 0, 'null' => false, 'signed' => false, 'comment' => '税率%',])
            ->addColumn('n_tax_price', 'decimal', ['precision' => 8, 'scale' => 2, 'default' => 0, 'null' => false, 'signed' => false, 'comment' => '不含税单价',])
            ->addColumn('packing_size', 'string', ['limit' => 300, 'default' => '', 'null' => false, 'signed' => false, 'comment' => '最少包装规格',])
            ->addColumn('packing_specification', 'string', ['limit' => 300, 'default' => '', 'null' => false, 'signed' => false, 'comment' => '包装规格',])
            ->addColumn('valid_start_time', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'default' => 0, 'signed' => false, 'comment' => '有效时间开始',])
            ->addColumn('valid_end_time', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'default' => 0, 'signed' => true, 'comment' => '有效时间结束',])

            ->addColumn('created_at', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'default' => 0, 'signed' => true, 'comment' => '创建时间',])
            ->addColumn('updated_at', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'default' => 0, 'signed' => true, 'comment' => '更新时间',])
            ->addColumn('deleted_at', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'default' => 0, 'signed' => true, 'comment' => '软删除',])
            ->create();
    }
}
