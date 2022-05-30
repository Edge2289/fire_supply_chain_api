<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/3/12
 * Time: 16:15
 */

namespace catchAdmin\salesManage\request;


use catcher\base\CatchRequest;

/**
 * Class SalesOrderRequest
 * @package catchAdmin\salesManage\request
 */
class SalesOrderRequest extends CatchRequest
{
    protected function rules(): array
    {
        return [
            'purchase_date|单据日期' => 'require',
            'user_id|销售人员' => 'require',
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