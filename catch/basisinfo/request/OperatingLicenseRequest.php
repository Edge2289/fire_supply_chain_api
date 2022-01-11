<?php
/**
 * Created by PhpStorm.
 * author: xiejiaqing
 * Note: Tired as a dog
 * Date: 2022/1/8
 * Time: 17:47
 */

namespace catchAdmin\basisinfo\request;


use catcher\base\CatchRequest;

/**
 * Class OperatingLicenseRequest
 * @package catchAdmin\basisinfo\request
 */
class OperatingLicenseRequest extends CatchRequest
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
            'operating_license_code|许可证编号' => 'require|max:100',
            'operation_mode|经营方式' => 'require',
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