<?php
/**
 * Created by PhpStorm.
 * author: xiejiaqing
 * Note: Tired as a dog
 * Date: 2022/1/8
 * Time: 17:48
 */

namespace catchAdmin\basisinfo\request;


use catcher\base\CatchRequest;

/**
 * Class RegistrationLicenseRequest
 * @package catchAdmin\basisinfo\request
 */
class RegistrationLicenseRequest extends CatchRequest
{
    public function __construct(array $params = null)
    {
        $this->param = $params;
        parent::__construct();
    }

    protected function rules(): array
    {
        return [
            'business_license_id|营业执照id' => 'require',
            'registration_license_code|备案号' => 'require|max:100',
            'registration_date|备案日期' => 'require',
            'operation_mode|经营方式' => 'require|max:200',
            'company_name|企业名称' => 'require|max:200',
            'legal_person|法人' => 'require|max:200',
            'incharge_person|企业负责人' => 'require|max:200',
            'residence|住所' => 'require|max:200',
            'residence|经营场所' => 'require|max:200',
            'warehouse_address|库房地址' => 'require|max:500',
        ];
    }

    protected function message()
    {

    }
}