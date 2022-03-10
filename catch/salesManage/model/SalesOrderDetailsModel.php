<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/3/6
 * Time: 22:55
 */

namespace catchAdmin\salesManage\model;


use catchAdmin\basisinfo\model\ProductBasicInfo;
use catchAdmin\basisinfo\model\ProductSku;
use catcher\base\CatchModel;
use think\model\relation\HasOne;

/**
 * Class SalesOrderDetailsModel
 * @package catchAdmin\salesManage\model
 */
class SalesOrderDetailsModel extends CatchModel
{
    protected $connection = 'business';

    protected $name = 'sales_order_details';

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