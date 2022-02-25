<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
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
    public function __construct(array $params = null)
    {
        $this->param = $params;
        parent::__construct();
    }

    protected function rules(): array
    {
        return [
            'company_name|企业名称' => 'require|max:200',
            'foreign_company|国外注册公司' => 'max:200',
            'unified_code|统一社会信用代码' => 'require',
//            'residence|住所' => 'require|max:200',
            'legal_person|法人' => 'require|max:200',
            'establish_date|登记日期' => 'require',
            'business_start_date|营业期限' => 'require',
            'business_scope|经营范围' => 'require|max:2000',
            'data_maintenance|资料维护' => 'require',
        ];
    }

    protected function message()
    {

    }
}