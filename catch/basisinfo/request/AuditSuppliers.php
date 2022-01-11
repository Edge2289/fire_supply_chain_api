<?php
/**
 * Created by PhpStorm.
 * author: xiejiaqing
 * Note: Tired as a dog
 * Date: 2022/1/11
 * Time: 10:21
 */

namespace catchAdmin\basisinfo\request;


use catcher\base\CatchRequest;

/**
 * Class AuditSuppliers
 * @package catchAdmin\basisinfo\request
 */
class AuditSuppliers extends CatchRequest
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

    }
}