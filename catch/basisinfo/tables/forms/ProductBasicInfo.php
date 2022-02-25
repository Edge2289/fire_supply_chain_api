<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/1/16
 * Time: 17:32
 */

namespace catchAdmin\basisinfo\tables\forms;


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
            self::select("factory_id", "生产厂家")->col(12)->clearable(true)->options(
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