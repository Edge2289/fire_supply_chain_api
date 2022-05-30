<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/1/17
 * Time: 21:00
 */

namespace catchAdmin\basisinfo\tables\forms;


use catcher\library\form\Form;
use fire\data\ProductDept;

/**
 * Class ProductQualification
 * @package catchAdmin\basisinfo\tables\forms
 */
class ProductQualification extends Form
{
    public function fields(): array
    {
        return [
            self::file("产品质量标准 / 技术要求 已核对，验收合格", "product_quality_url")->col(8),
            self::file("进口产品相关证件 已核对，验收合格", "imported_documents_url")->col(8),
            self::file("出厂检验报告 / 产品合格证 / 产品说明书 (盖红章) 已核对，验收合格", "report_delivery_url")->col(8),
            self::file("产品外观", "product_appearance_url")->col(8),
            self::file("产品注册证", "product_register_url")->col(8),
        ];
    }
}