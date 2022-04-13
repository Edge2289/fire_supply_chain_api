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

/**
 * Class ProductSku
 * @package catchAdmin\basisinfo\model
 */
class ProductSku extends CatchModel
{
    protected $name = "product_sku";
    protected $pk = 'id';

    public function hasProductEntity(): HasMany
    {
        return $this->hasMany(ProductEntity::class, "product_sku_id", "id");
    }
}