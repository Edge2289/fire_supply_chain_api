<?php
/**
 * Created by PhpStorm.
 * author: xiejiaqing
 * Note: Tired as a dog
 * Date: 2022/1/16
 * Time: 20:39
 */

namespace catchAdmin\basisinfo\request;


use catcher\base\CatchRequest;

/**
 * Class ProductBasicInfoRequest
 * @package catchAdmin\basisinfo\request
 */
class ProductBasicInfoRequest extends CatchRequest
{
    protected function rules(): array
    {
        return [
            'product_name|产品名称' => 'require|max:200',
            'factory_id|生产厂家' => 'require',
            'storage_conditions|储存条件' => 'require|max:200',
            'data_maintenance|资料维护' => 'require',
            'product_img|产品图' => 'require',
        ];
    }
}