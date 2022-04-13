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

/**
 * Class ProductRecord
 * @package catchAdmin\basisinfo\tables\forms
 */
class ProductRecord extends Form
{
    public function fields(): array
    {
        return [
            self::input("record_product_categories", "产品分类")->col(12)->required(),
            self::input("record_code", "备案号")->col(12)->required(),
            self::input("recorder_org_code", "组织机构代码")->col(12)->required(),
            self::input("record_name", "备案人名称")->col(12)->required(),
            self::input("record_creator_company_address", "备案人生产地址")->col(12)->required(),
            self::input("record_proxy_name", "代理人")->col(12)->required(),
            self::input("record_proxy_address", "代理人注册住址")->col(12)->required(),
            self::input("product_desc", "产品描述")->col(12)->required(),
            self::input("preliminary_use", "预备用途")->col(12)->required(),
            self::input("record_department", "备案单位")->col(12)->required(),
            self::date("record_time", "备案日期")->col(12)->required(),
            self::input("record_remark", "备注")->col(12)->required(),
        ];
    }
}