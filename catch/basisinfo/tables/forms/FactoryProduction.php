<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/1/13
 * Time: 20:13
 */

namespace catchAdmin\basisinfo\tables\forms;


use catcher\library\form\Form;

/**
 * Class FactoryProduction
 * @package catchAdmin\basisinfo\tables\forms
 */
class FactoryProduction extends Form
{
    public function fields(): array
    {
        return [
            self::input("license_code", "许可证编号")->col(12)->required(),
            self::input("company_name", "企业名称")->col(12)->required(),
            self::dateRange("business_start_date", "有效期限", "business_start_date", "business_end_date")->editable(true)->required(),
            self::input("legal_person", "法人")->col(12)->required(),
            self::input("head_name", "企业负责人")->col(12)->required(),
            self::input("production_address", "生产地址")->required(),
            self::input("residence", "住所")->required(),
            self::textarea("production_scope", "生产范围")->required(),
            self::input("license_department", "发证部门")->col(12),
            self::input("license_date", "发证日期")->col(12),
        ];
    }
}