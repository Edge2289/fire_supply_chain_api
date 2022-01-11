<?php
/**
 * Created by PhpStorm.
 * author: xiejiaqing
 * Note: Tired as a dog
 * Date: 2022/1/8
 * Time: 17:49
 */

namespace catchAdmin\basisinfo\request;


use catcher\base\CatchRequest;

/**
 * Class SuppleInfoRequest
 * @package catchAdmin\basisinfo\request
 */
class SuppleInfoRequest extends CatchRequest
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
            'name|被授权人姓名' => 'require|max:100',
            'certid|证件号码' => 'require',
            'phone|手机号' => 'require|max:200',
            'email|邮箱' => 'require|max:200',
            'license_start_date|授权日期' => 'require|max:200',
            'invoice_head|发票抬头' => 'require|max:200',
            'invoice_no|税号' => 'require|max:200',
            'invoice_bank|开户银行' => 'require|max:200',
            'invoice_bank_no|银行账号' => 'require|max:500',
        ];
    }

    protected function message()
    {

    }
}