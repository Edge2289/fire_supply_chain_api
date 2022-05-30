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

class BusinessAttachment extends Migrator
{
    public function change()
    {
        $table = $this->table('business_attachment', ['engine' => 'InnoDB', 'collation' => 'utf8mb4_general_ci', 'comment' => '资质与附件', 'id' => 'id', 'signed' => true, 'primary_key' => ['id']]);
        $table->addColumn('business_license_id', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '营业执照id',])
            ->addColumn('business_license_url', 'string', ['limit' => 300, 'default' => '', 'signed' => true, 'comment' => '营业执照url',])
            ->addColumn('check_business_license', 'integer', ['limit' => MysqlAdapter::INT_TINY, 'default' => 0, 'signed' => true, 'comment' => '核对营业执照',])
            ->addColumn('production_license_url', 'string', ['limit' => 300, 'default' => "", 'signed' => true, 'comment' => '医疗器械经营许可证url',])
            ->addColumn('check_production_license', 'integer', ['limit' => MysqlAdapter::INT_TINY, 'default' => 0, 'signed' => true, 'comment' => '核对医疗器械经营许可证',])
            ->addColumn('record_certificate_url', 'string', ['limit' => 300, 'default' => "", 'signed' => true, 'comment' => '第二类医疗器械经营备案凭证/生产备案凭证url',])
            ->addColumn('check_record_certificate', 'integer', ['limit' => MysqlAdapter::INT_TINY, 'default' => 0, 'signed' => true, 'comment' => '核对第二类医疗器械经营备案凭证/生产备案凭证',])
            ->addColumn('invoice_information_url', 'string', ['limit' => 300, 'default' => "", 'signed' => true, 'comment' => '开票资料',])
            ->addColumn('check_invoice_information', 'integer', ['limit' => MysqlAdapter::INT_TINY, 'default' => 0, 'signed' => true, 'comment' => '核对开票资料',])
            ->addColumn('person_authorization_url', 'string', ['limit' => 300, 'default' => '', 'signed' => false, 'comment' => '法人委托授权书',])
            ->addColumn('check_person_authorization', 'integer', ['limit' => MysqlAdapter::INT_TINY, 'default' => 0, 'signed' => false, 'comment' => '核对法人委托授权书',])
            ->addColumn('assurance_agreement_url', 'string', ['limit' => 300, 'default' => '', 'signed' => false, 'comment' => '质量保证协议书',])
            ->addColumn('check_assurance_agreement', 'integer', ['limit' => MysqlAdapter::INT_TINY, 'default' => 0, 'signed' => false, 'comment' => '核对质量保证协议书',])
            ->addColumn('delivery_template_url', 'string', ['limit' => 300, 'default' => '', 'signed' => true, 'comment' => '出库单模板',])
            ->addColumn('check_delivery_template', 'integer', ['limit' => MysqlAdapter::INT_TINY, 'default' => 0, 'signed' => true, 'comment' => '核对出库单模板',])
            ->addColumn('seal_filing_template_url', 'string', ['limit' => 300, 'default' => '', 'signed' => false, 'comment' => '印章备案模板',])
            ->addColumn('check_seal_filing_template', 'integer', ['limit' => MysqlAdapter::INT_TINY, 'default' => 0, 'signed' => false, 'comment' => '核对印章备案模板',])
            ->addColumn('system_survey_form_url', 'string', ['limit' => 300, 'default' => '', 'signed' => false, 'comment' => '质量体系调查表',])
            ->addColumn('check_system_survey_form', 'integer', ['limit' => MysqlAdapter::INT_TINY, 'default' => 0, 'signed' => false, 'comment' => '核对质量体系调查表',])
            ->addColumn('annual_report_url', 'string', ['limit' => 300, 'default' => '', 'signed' => false, 'comment' => '年度报告',])
            ->addColumn('check_annual_report', 'integer', ['limit' => MysqlAdapter::INT_TINY, 'default' => 0, 'signed' => false, 'comment' => '核对年度报告',])
            ->addColumn('created_at', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '创建时间',])
            ->addColumn('updated_at', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '更新时间',])
            ->addColumn('deleted_at', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'null' => false, 'default' => 0, 'signed' => true, 'comment' => '软删除',])
            ->create();
    }
}
