<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/1/16
 * Time: 20:58
 */

namespace catchAdmin\basisinfo\model;


use catcher\base\CatchModel;
use think\model\relation\HasMany;
use think\model\relation\HasOne;

/**
 * Class ProductSku
 * @package catchAdmin\basisinfo\model
 */
class ProductSku extends CatchModel
{
    protected $name = "product_sku";
    protected $pk = 'id';

    protected $fieldNumberToEmpty = [
        'unit_price_1', 'unit_price_2', 'unit_price_3', 'unit_price_4',
        'procurement_price_1', 'procurement_price_2',
    ];

    /**
     * @return HasOne
     * @author 1131191695@qq.com
     */
    public function hasProductBasicInfo(): HasOne
    {
        return $this->hasOne(ProductBasicInfo::class, "id", "product_id");
    }

    /**
     * @return HasMany
     * @author 1131191695@qq.com
     */
    public function hasProductEntity(): HasMany
    {
        return $this->hasMany(ProductEntity::class, "product_sku_id", "id");
    }
}