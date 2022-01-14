<?php
/**
 * Created by PhpStorm.
 * author: xiejiaqing
 * Note: Tired as a dog
 * Date: 2022/1/11
 * Time: 15:06
 */

namespace catchAdmin\basisinfo\model;


use catcher\base\CatchModel;

/**
 * Class Factory
 * @package catchAdmin\basisinfo\model
 */
class Factory extends CatchModel
{
    protected $name = 'factory';

    protected $pk = 'id';

    protected $field = [
        'id',
        'factory_code',
        'factory_type',
        'company_name',
        'company_name_en',
        'business_license_url',
        'contract_url',
        'unified_code',
        'residence',
        'legal_person',
        'registration_date',
        'registered_capital',
        'business_start_date',
        'business_end_date',
        'business_date_long',
        'establish_date',
        'data_maintenance',
        'other',
        'audit_status',
        'audit_info',
        'status',
        'audit_user_id',
        'audit_user_name',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function getBusinessStartDateAttr($value)
    {
        return $value? date("Y-m-d", $value): $value;
    }
    public function getBusinessEndDateAttr($value)
    {
        return $value? date("Y-m-d", $value): $value;
    }

    public function getRegistrationDateAttr($value)
    {
        return $value? date("Y-m-d", $value): $value;
    }

    public function getEstablishDateAttr($value)
    {
        return $value? date("Y-m-d", $value): $value;
    }

    /**
     * 列表
     *
     * @time 2020年01月09日
     * @param $params
     * @throws \think\db\exception\DbException
     * @return \think\Paginator
     */
    public function getList()
    {
        return $this->catchSearch()
            ->paginate();
    }
}