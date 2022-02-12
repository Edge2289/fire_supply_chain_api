<?php
/**
 * Created by PhpStorm.
 * author: xiejiaqing
 * Note: Tired as a dog
 * Date: 2022/2/3
 * Time: 23:25
 */

namespace catchAdmin\purchase\request;


use catcher\base\CatchRequest;

/**
 * Class PurchaseOrderRequest
 * @package catchAdmin\purchase\request
 */
class PurchaseOrderRequest extends CatchRequest
{
    protected function rules(): array
    {
        return [
            'id|采购订单' => 'require',
            'audit_status|审核状态' => 'require',
            'audit_info|审核信息' => 'require|max:100'
        ];
    }

    // 检查商品
    protected function checkGoods($value, $rule, $data = [])
    {
        // bool | message
        return true;
    }

    protected function message()
    {

    }
}