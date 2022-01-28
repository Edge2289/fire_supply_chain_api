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

class PurchaseOrderDetails extends Migrator
{
    public function change()
    {
        $table = $this->table('purchase_order_details', ['engine' => 'Myisam', 'collation' => 'utf8mb4_general_ci', 'comment' => '营业执照表', 'id' => 'id', 'signed' => true, 'primary_key' => ['id']]);
        $table->addColumn('purchase_order_id', 'string', ['limit' => 300, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '采购订单id',])
            ->addColumn('product_id', 'string', ['limit' => 300, 'null' => false, 'default' => '', 'signed' => false, 'comment' => '产品id',])
            ->addColumn('price', 'string', ['limit' => 300, 'default' => "", 'signed' => false, 'comment' => '价格',])
            ->addColumn('tax_price', 'string', ['limit' => 300, 'default' => '', 'signed' => false, 'comment' => '税价格',])
            ->addColumn('quantity', 'string', ['limit' => 100, 'null' => false, 'default' => '', 'signed' => false, 'comment' => '数量',])
            ->addColumn('receipt_quantity', 'string', ['limit' => 60, 'null' => false, 'default' => "", 'signed' => false, 'comment' => '收货数量',])
            ->addColumn('warehousing_quantity', 'string', ['limit' => 60, 'null' => false, 'default' => "", 'signed' => false, 'comment' => '入库数量',])
            ->addColumn('return_quantity', 'string', ['limit' => 60, 'null' => false, 'default' => "", 'signed' => false, 'comment' => '退货数量',])
            // 旧数据维护 资格证之类的

            ->addColumn('created_at', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '创建时间',])
            ->addColumn('updated_at', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '更新时间',])
            ->addColumn('deleted_at', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '软删除',])
            ->create();
    }
}