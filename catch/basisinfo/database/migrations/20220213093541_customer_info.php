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
use \Phinx\Db\Adapter\MysqlAdapter;

class CustomerInfo extends Migrator
{
    public function change()
    {
        $table = $this->table('customer_info', ['engine' => 'Myisam', 'collation' => 'utf8mb4_general_ci', 'comment' => '补充信息', 'id' => 'id', 'signed' => true, 'primary_key' => ['id']]);
        $table->addColumn('customer_type', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '客户类型{1:经销商,2:医院}',])
            ->addColumn('company_name', 'string', ['limit' => 100, 'null' => true, 'default' => '', 'signed' => false, 'comment' => '企业名称',])
            ->addColumn('operating_license_code', 'string', ['limit' => 300, 'default' => '', 'signed' => true, 'comment' => '营业执照url',])
            ->addColumn('business_types', 'string', ['limit' => 100, 'null' => true, 'default' => '', 'signed' => false, 'comment' => '医疗机构类别',])
            ->addColumn('business_nature', 'string', ['limit' => 100, 'null' => true, 'default' => '', 'signed' => false, 'comment' => '经营性质',])
            ->addColumn('hos_name', 'string', ['limit' => 100, 'null' => true, 'default' => '', 'signed' => false, 'comment' => '医疗机构名称',])
            ->addColumn('hos_code', 'string', ['limit' => 100, 'null' => true, 'default' => '', 'signed' => false, 'comment' => '登记号',])
            ->addColumn('legal_person', 'string', ['limit' => 60, 'null' => true, 'default' => '', 'signed' => false, 'comment' => '法人',])
            ->addColumn('incharge_person', 'string', ['limit' => 60, 'null' => true, 'default' => "", 'signed' => true, 'comment' => '主要负责人',])
            ->addColumn('detailed_address', 'string', ['limit' => 300, 'null' => true, 'default' => '', 'signed' => false, 'comment' => '地址',])
            ->addColumn('effective_start_date', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => true, 'default' => 0, 'signed' => true, 'comment' => '有效期开始',])
            ->addColumn('effective_end_date', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => true, 'default' => 0, 'signed' => true, 'comment' => '有效期结束',])
            ->addColumn('certification_date', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => true, 'default' => 0, 'signed' => true, 'comment' => '发证日期',])
            ->addColumn('certification_department', 'string', ['limit' => 100, 'null' => true, 'default' => '', 'signed' => false, 'comment' => '发证机关',])
            ->addColumn('business_scope', 'string', ['limit' => 100, 'null' => true, 'default' => '', 'signed' => false, 'comment' => '诊疗科目',])
            ->addColumn('audit_status', 'integer', ['limit' => MysqlAdapter::INT_TINY, 'null' => true, 'default' => 0, 'signed' => true, 'comment' => '审核状态 {0:未审核,1:已审核,2:审核失败}',])
            ->addColumn('audit_info', 'string', ['limit' => 300, 'null' => true, 'default' => '', 'signed' => true, 'comment' => '审核信息',])
            ->addColumn('status', 'integer', ['limit' => MysqlAdapter::INT_TINY, 'null' => true, 'default' => 0, 'signed' => true, 'comment' => '是否使用 {0:停用,1:启动}',])
            ->addColumn('audit_user_id', 'integer', ['limit' => MysqlAdapter::INT_MEDIUM, 'null' => true, 'default' => 0, 'signed' => true, 'comment' => '审核人id',])
            ->addColumn('audit_user_name', 'string', ['limit' => 60, 'null' => true, 'default' => '', 'signed' => true, 'comment' => '审核人名字',])
            ->addColumn('created_at', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '创建时间',])
            ->addColumn('updated_at', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '更新时间',])
            ->addColumn('deleted_at', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '软删除',])
            ->create();
    }
}
