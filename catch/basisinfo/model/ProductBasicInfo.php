<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/1/15
 * Time: 22:32
 */

namespace catchAdmin\basisinfo\model;


use catcher\base\CatchModel;

/**
 * Class ProductBasicInfo
 * @package catchAdmin\basisinfo\model
 */
class ProductBasicInfo extends CatchModel
{
    protected $name = "product_basic_info";

    public function withRegistered()
    {
        return $this->hasOne(ProductRegistered::class, "product_id", "id");
    }

    public function withRecord()
    {
        return $this->hasOne(ProductRecord::class, "product_id", "id");
    }

    public function withFactory()
    {
        return $this->hasOne(Factory::class, "id", "factory_id");
    }

    public function getList()
    {
        return $this->with([
            "withRegistered" => function ($rQuery) {
                $rQuery->field(['product_id', 'registered_code', 'end_time']);
            },
            "withRecord" => function ($eQuery) {
                $eQuery->field(['product_id', 'record_code']);
            },
            "withFactory" => function ($fQuery) {
                $fQuery->field(['id', 'company_name']);
            }
        ])->catchSearch()->order("id desc")
            ->paginate();
    }
}