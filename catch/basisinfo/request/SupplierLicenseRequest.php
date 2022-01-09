<?php
/**
 * Created by PhpStorm.
 * author: xiejiaqing
 * Note: Tired as a dog
 * Date: 2022/1/8
 * Time: 17:36
 */

namespace catchAdmin\basisinfo\request;


use catchAdmin\basisinfo\model\SupplierLicense;
use catchAdmin\permissions\model\Users;
use catcher\base\CatchRequest;

/**
 * Class SuplierLicenseRequest
 * @package catchAdmin\basisinfo\request
 */
class SupplierLicenseRequest extends CatchRequest
{
    public function __construct(array $params)
    {
        $this->param = $params;
        parent::__construct();
    }

    protected function rules(): array
    {
        /**
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
            'other', // 备注
            'status', // 状态
         */
        return [
            'company_name|企业名称' => 'require|max:200',
            'foreign_company|国外注册公司' => 'max:200',
            'unified_code|统一社会信用代码'    => 'require|unique:'.SupplierLicense::class,
        ];
    }

    protected function message()
    {
        // TODO: Implement message() method.
    }
}