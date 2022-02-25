<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/1/12
 * Time: 16:56
 */

namespace catchAdmin\basisinfo\model;


use catcher\base\CatchModel;

/**
 * Class CustomerLicense
 * @package catchAdmin\basisinfo\model
 */
class CustomerLicense extends CatchModel
{
    protected $name = "customer_license";

    protected $pk = 'id';

    public function getEstablishDateAttr($value)
    {
        return $this->toDate($value);
    }

    public function getBusinessEndDateAttr($value)
    {
        return $this->toDate($value);
    }

    public function getBusinessStartDateAttr($value)
    {
        return $this->toDate($value);
    }

    public function getRegistrationDateAttr($value)
    {
        return $this->toDate($value);
    }

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
     * @time 2020年01月09日
     * @param $params
     * @return \think\Paginator
     * @throws \think\db\exception\DbException
     */
    public function getList()
    {
        return $this->catchSearch()->order("id desc")
            ->paginate();
    }
}