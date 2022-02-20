<?php
/**
 * Created by PhpStorm.
 * author: xiejiaqing
 * Note: Tired as a dog
 * Date: 2022/1/13
 * Time: 20:26
 */

namespace catchAdmin\basisinfo\tables\forms;


use catcher\library\form\Form;

/**
 * Class FactoryRecord
 * @package catchAdmin\basisinfo\tables\forms
 */
class FactoryRecord extends Form
{
    public function fields(): array
    {
        return [
            self::hidden("factory_id", 0),
            self::file("备案凭证", "record_license_url")->required(),
            self::input("record_code", "备案号")->col(12)->required(),
            self::input("company_name", "企业名称")->col(12)->required(),
            self::input("legal_person", "法人")->col(12)->required(),
            self::input("head_name", "企业负责人")->col(12)->required(),
            self::input("production_address", "生产地址")->required(),
            self::textarea("production_scope", "生产范围")->required(),
            self::input("record_department", "备案部门")->required(),
            self::date("record_date", "备案日期")->col(12),
        ];
    }
}