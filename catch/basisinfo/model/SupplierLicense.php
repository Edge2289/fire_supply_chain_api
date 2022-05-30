<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/1/6
 * Time: 09:44
 */

namespace catchAdmin\basisinfo\model;


use catcher\base\CatchModel;

/**
 * Class SupplierLicense
 * @package catchAdmin\basisinfo\model
 */
class SupplierLicense extends CatchModel
{
    protected $name = 'business_license';

    protected $pk = 'id';

    protected $fieldToTime = [
        'registration_date', 'business_start_date', 'business_end_date', 'establish_date'
    ];

    // 字段
    protected $field = [
        'id', //
        'company_name', // 企业名称
        'foreign_company', // 国外注册公司
        'company_type', // 类型
        'unified_code', // 统一社会信用代码
        'residence', // 统一社会信用代码
        'legal_person', // 法人
        'registration_date', // 成立日期
        'registered_capital', // 注册资本
        'legal_person', // 法人
        'establish_date', // 营业期限
        'business_end_date', // 营业期限
        'business_start_date', // 营业期限
        'business_date_long', // 营业期限长期
        'business_scope', // 经营范围
        'data_maintenance', // 资料维护
        'other', // 备注
        'audit_status', // 审核状态
        'audit_info', // 审核信息
        'status', // 状态
        'created_at', // 创建时间
        'updated_at', // 更新时间
        'deleted_at', // 删除状态，null 未删除 timestamp 已删除
    ];

    /**
     * 列表
     *
     * @return array|mixed
     * @throws \think\db\exception\DbException
     * @author 1131191695@qq.com
     */
    public function getList()
    {
        return $this->catchSearch()->order("id desc")
            ->paginate();
    }

    /**
     * 获取允许选择的供应商
     *
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author 1131191695@qq.com
     */
    public function getSupplier()
    {
        $data = $this->where("status", 1)
            ->where("audit_status", 1)->select();
        $map = [];
        foreach ($data as $datum) {
            $map[] = [
                'value' => (string)$datum['id'],
                'label' => $datum['company_name'],
            ];
        }
        return $map;
    }
}