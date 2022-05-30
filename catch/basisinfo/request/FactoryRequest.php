<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/1/11
 * Time: 15:21
 */

namespace catchAdmin\basisinfo\request;


use catchAdmin\basisinfo\model\Factory;
use catcher\base\CatchRequest;

/**
 * 厂家信息
 *
 * Class FactoryRequest
 * @package catchAdmin\basisinfo\request
 */
class FactoryRequest extends CatchRequest
{
    protected function rules(): array
    {
        return [
            'company_name|企业名称' => 'require|max:200',
            'foreign_company|国外注册公司' => 'max:200',
            'unified_code|统一社会信用代码' => 'require|unique:' . Factory::class,
            'residence|住所' => 'require|max:200',
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