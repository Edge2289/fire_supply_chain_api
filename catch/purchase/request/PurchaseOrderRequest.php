<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
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
            'purchase_date|采购日期' => 'require',
            'user_id|采购人员' => 'require',
            'supplier_id|供应商' => 'require|max:100',
            'goods_details|商品' => 'require'
        ];
    }

    // 检查商品
    protected function checkGoods($value, $rule, $data = [])
    {
//        dd($value);
        // bool | message
//        return false;
    }

    protected function message()
    {

    }
}