<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/1/16
 * Time: 17:32
 */

namespace catchAdmin\basisinfo\tables\forms;


use catchAdmin\basisinfo\model\ProductCategory as ProductCategoryModel;
use catchAdmin\permissions\model\Roles;
use catcher\library\form\Form;

/**
 * Class ProductBasicInfo
 * @package catchAdmin\basisinfo\tables\forms
 */
class ProductBasicInfo extends Form
{
    public function fields(): array
    {
        return [
            self::input("product_name", "产品名称")->col(12)->required(),
            self::input("goods_name", "商品名称")->col(12)->required(),
            self::select("factory_id", "生产厂家")->col(12)->clearable(true)->options(
                function () {
                    $data = [];
                    foreach (\catchAdmin\basisinfo\model\Factory::where('audit_status', 1)->field(['id as value', 'company_name as label'])->select()->toArray() as $datum) {
                        $data[] = [
                            "value" => (string)$datum["value"],
                            "label" => $datum["label"]
                        ];
                    }
                    return $data;
                }
            )->required(),
            self::select("product_type", "产品类型", "1")->col(12)->options(
                self::options()->add('III类', "1")
                    ->add('II类', "2")
                    ->add('I类', "3")
                    ->add('非医疗器械', "4")->render()
            )->required(),

            self::cascader('product_category_id', '产品类别', [])->options(
                ProductCategoryModel::field(['id', 'name', 'p_id'])
                    ->select()->toTree(0, 'p_id')
            )->col(12)->props(self::props('name', 'id', [
                'checkStrictly' => true
            ]))->filterable(true)->clearable(true)->required()->style(['width' => '100%']),

            self::input("storage_conditions", "储运条件")->col(12),
            self::radio("data_maintenance", "资料维护")->options(
                self::options()->add('注册证', 1)
                    ->add('备案凭证', 2)
                    ->add('非医疗器械', 3)->render()
            ),
        ];
    }
}