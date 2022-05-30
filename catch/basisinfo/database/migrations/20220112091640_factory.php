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

class Factory extends Migrator
{
    /**
     * 厂家迁移
     * @author 1131191695@qq.com
     */
    public function change()
    {
        $table = $this->table('factory', ['engine' => 'InnoDB', 'collation' => 'utf8mb4_general_ci', 'comment' => '厂家营业执照信息', 'id' => 'id', 'signed' => true, 'primary_key' => ['id']]);
        $table->addColumn('factory_code', 'string', ['limit' => 20, 'null' => false, 'default' => '', 'signed' => true, 'comment' => '厂家编号',])
            ->addColumn('factory_type', 'integer', ['limit' => MysqlAdapter::INT_TINY, 'null' => false, 'default' => 1, 'signed' => false, 'comment' => '厂家类型{1:国内公司,2:国外公司}',])
            ->addColumn('company_name', 'string', ['limit' => 300, 'default' => '', 'signed' => true, 'comment' => '企业名称',])
            ->addColumn('company_name_en', 'string', ['limit' => 60, 'default' => '', 'signed' => true, 'comment' => '企业英文名称',])
            ->addColumn('business_license_url', 'string', ['limit' => 300, 'default' => '', 'signed' => true, 'comment' => '营业执照url',])
            ->addColumn('contract_url', 'string', ['limit' => 300, 'default' => '', 'signed' => true, 'comment' => '合同url',])
            ->addColumn('unified_code', 'string', ['limit' => 100, 'default' => '', 'signed' => false, 'comment' => '统一社会信用代码',])
            ->addColumn('residence', 'string', ['limit' => 300, 'default' => '', 'signed' => false, 'comment' => '住所',])
            ->addColumn('legal_person', 'string', ['limit' => 60, 'default' => '', 'signed' => false, 'comment' => '法人',])
            ->addColumn('registration_date', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'default' => 0, 'signed' => true, 'comment' => '成立日期',])
            ->addColumn('registered_capital', 'string', ['limit' => 100, 'default' => '', 'signed' => false, 'comment' => '注册资本',])
            ->addColumn('business_start_date', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'default' => 0, 'signed' => true, 'comment' => '营业期限开始',])
            ->addColumn('business_end_date', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'default' => 0, 'signed' => true, 'comment' => '营业期限结束',])
            ->addColumn('business_date_long', 'integer', ['limit' => MysqlAdapter::INT_TINY, 'default' => 0, 'signed' => true, 'comment' => '是否长期 {0:否,1:是}',])
            ->addColumn('establish_date', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'default' => 0, 'signed' => true, 'comment' => '登记日期',])
            ->addColumn('data_maintenance', 'string', ['limit' => 60, 'default' => "", 'signed' => false, 'comment' => '资料维护 1 经营许可证 2 经营备案凭证',])
            ->addColumn('business_scope', 'string', ['limit' => 300, 'default' => '', 'signed' => false, 'comment' => '经营范围',])
            ->addColumn('other', 'string', ['limit' => 300, 'default' => '', 'signed' => false, 'comment' => '备注',])
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
