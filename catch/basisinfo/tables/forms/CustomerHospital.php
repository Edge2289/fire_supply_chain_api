<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/2/14
 * Time: 22:22
 */

namespace catchAdmin\basisinfo\tables\forms;


use catcher\library\form\Form;

/**
 * Class CustomerHospital
 * @package catchAdmin\basisinfo\tables\forms
 */
class CustomerHospital extends Form
{
    public function fields(): array
    {
        return [
            self::input("operating_license_code", "营业执照")->col(12),
            self::input("company_name", "公司名称")->col(12),
            self::input("business_types", "医疗机构类别")->col(12),
            self::input("business_nature", "经营性质")->col(12),
            self::input("hos_name", "医疗机构名称")->col(12)->required(),
            self::input("hos_code", "登记号")->col(12)->required(),
            self::input("legal_person", "法人")->required(),
            self::input("incharge_person", "主要负责人")->required(),
            self::textarea("detailed_address", "地址")->required(),
            self::date("effective_start_date", "有限期限(开始)")->editable(true)->col(12)->required(),
            self::date("effective_end_date", "有限期限(结束)")->editable(true)->col(12)->required(),
            self::date("certification_date", "发证日期")->editable(true)->col(12),
            self::input("certification_department", "发证机关")->col(12),
            self::input("business_scope", "诊疗科目")->col(12),
        ];
    }
}