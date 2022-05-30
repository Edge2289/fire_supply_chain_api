<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/1/17
 * Time: 17:08
 */

namespace catchAdmin\basisinfo\request;


use catcher\base\CatchRequest;

/**
 * Class ProductRegisteredRequest
 * @package catchAdmin\basisinfo\request
 */
class ProductRegisteredRequest extends CatchRequest
{
    protected function rules(): array
    {
        return [
            'product_id|产品id' => 'require',
            'registered_code|注册证编号' => 'require|max:200',
            'registered_address|生产地址' => 'require',
            'registered_name|注册人名称' => 'require',
            'registered_company_address|注册人住所' => 'require',
            'record_proxy_name|代理人名称' => 'require',
            'registered_proxy_address|代理人住址' => 'require',
            'comprise_desc|结构及组成' => 'require',
            'product_desc|适用范围' => 'require',
            'registered_time|批准日期' => 'require',
            'end_time|有效期至' => 'require',
        ];
    }
}