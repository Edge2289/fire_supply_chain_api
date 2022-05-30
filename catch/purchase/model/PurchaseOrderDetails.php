<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/1/30
 * Time: 23:46
 */

namespace catchAdmin\purchase\model;


use catchAdmin\basisinfo\model\ProductBasicInfo;
use catchAdmin\basisinfo\model\ProductSku;
use catcher\base\CatchModel;
use think\model\relation\HasOne;

/**
 * Class PurchaseOrderDetails
 * @package catchAdmin\purchase\model
 */
class PurchaseOrderDetails extends CatchModel
{
    // 这个根据登陆的账号去获取链接
    protected $connection = 'business';

    protected $name = "purchase_order_details";

    public function hasProductData(): HasOne
    {
        return $this->hasOne(ProductBasicInfo::class, "id", "product_id");
    }

    public function hasProductSkuData(): HasOne
    {
        return $this->hasOne(ProductSku::class, "id", "product_sku_id");
    }

}