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
        $i = $_GET['i'];
        $data = [
            self::input("license_code", "许可证编号")->col(12)->required(),
            self::input("company_name", "企业名称")->col(12)->required(),
            self::date("business_start_date", "有效期限(开始)")->col(12)->editable(true),
            self::date("business_end_date", "有效期限(结束)")->col(12)->editable(true),
            self::input("legal_person", "法人")->col(12)->required(),
            self::input("head_name", "企业负责人")->col(12)->required(),
            self::input("production_address", "生产地址")->required(),
            self::input("residence", "住所")->required(),
            self::textarea("production_scope", "生产范围")->required(),
            self::input("license_department", "发证部门")->col(12),
            self::input("license_date", "发证日期")->col(12),
        ];
        if ($i == 2) {
            $map = [];
            foreach ($data as $v) {
                $map[] = $v->disabled(true);
            }
            $data = $map;
        }
        return $data;
    }
}