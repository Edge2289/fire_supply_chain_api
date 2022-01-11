<?php
/**
 * Created by PhpStorm.
 * author: xiejiaqing
 * Note: Tired as a dog
 * Date: 2022/1/11
 * Time: 15:21
 */

namespace catchAdmin\basisinfo\request;


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
            'id|营业执照id' => 'require',
            'audit_status|审核状态' => 'require',
            'audit_info|审核信息' => 'require|max:100'
        ];
    }

    protected function message()
    {
        /**
         * factory_id
        factory_id
        企业名称	factory_name		公司名称(外文)	factory_name
        filing_certificate_id		公司名称(中文)	outside_factory_cname
        filing_certificate_key
        filing_certificate_url
        备案日期	filing_date
        备案部门	filing_department
        备案凭证	filing_file_name
        备案号	filing_number
        法人	legal_person
        生产地址	production_address
        生产范围	production_range
        住所	residence
        企业负责人	responsible_person
         */
    }
}