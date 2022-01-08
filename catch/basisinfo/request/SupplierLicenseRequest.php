<?php
/**
 * Created by PhpStorm.
 * author: xiejiaqing
 * Note: Tired as a dog
 * Date: 2022/1/8
 * Time: 17:36
 */

namespace catchAdmin\basisinfo\request;


use catchAdmin\permissions\model\Users;
use catcher\base\CatchRequest;

/**
 * Class SuplierLicenseRequest
 * @package catchAdmin\basisinfo\request
 */
class SupplierLicenseRequest extends CatchRequest
{
    protected function rules(): array
    {
        // TODO: Implement rules() method.
        return [
            'username|用户名' => 'require|max:20',
            'password|密码' => 'require|min:5|max:12',
            'email|邮箱'    => 'require|email|unique:'.Users::class,
        ];
    }

    protected function message()
    {
        // TODO: Implement message() method.
    }
}