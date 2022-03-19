<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/3/14
 * Time: 20:48
 */

namespace catchAdmin\salesManage\model;


use catchAdmin\basisinfo\model\ProductBasicInfo;
use catchAdmin\basisinfo\model\ProductSku;
use catcher\base\CatchModel;
use think\model\relation\HasOne;

/**
 * Class OutboundOrderDetails
 * @package catchAdmin\salesManage\model
 */
class OutboundOrderDetails extends CatchModel
{
    protected $connection = 'business';

    protected $name = 'outbound_order_details';

    protected $pk = 'id';

    public function hasProductData(): HasOne
    {
        return $this->hasOne(ProductBasicInfo::class, "id", "product_id");
    }

    public function hasProductSkuData(): HasOne
    {
        return $this->hasOne(ProductSku::class, "id", "product_sku_id");
    }

}