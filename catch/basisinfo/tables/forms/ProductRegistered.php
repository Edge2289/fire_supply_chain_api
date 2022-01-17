<?php
/**
 * Created by PhpStorm.
 * author: xiejiaqing
 * Note: Tired as a dog
 * Date: 2022/1/17
 * Time: 21:00
 */

namespace catchAdmin\basisinfo\tables\forms;


use catcher\library\form\Form;

/**
 * Class ProductRegistered
 * @package catchAdmin\basisinfo\tables\forms
 */
class ProductRegistered extends Form
{
    public function fields(): array
    {
        return [
            self::image("registered_license_url", "注册证图片")->required(),
            self::select("registered_product_categories", "产品分类")->col(12)->clearable(true)->options(
                \catchAdmin\basisinfo\model\Factory::where('audit_status', 1)->field(['id as value', 'company_name as label'])->select()->toArray()
            ),
            self::input("storage_conditions", "储运条件")->col(12)->required(),
            self::radio("data_maintenance", "资料维护", 1)->col(12)->options(
                self::options()->add('注册证', 1)
                    ->add('备案凭证', 2)->render()
            ),
            self::image("产品图片", "product_img")->required(),
        ];
    }
}