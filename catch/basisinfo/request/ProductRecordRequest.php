<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/1/17
 * Time: 17:14
 */

namespace catchAdmin\basisinfo\request;


use catcher\base\CatchRequest;

/**
 * Class ProductRecordRequest
 * @package catchAdmin\basisinfo\request
 */
class ProductRecordRequest extends CatchRequest
{
    protected function rules(): array
    {
        return [
            'product_id|产品id' => 'require',
            'record_product_categories|产品分类' => 'require',
            'record_code|备案号' => 'require|max:200',
            'record_name|备案人名称' => 'require',
            'record_time|备案日期' => 'require',
        ];
    }
}