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
 * Class ProductRegistered
 * @package catchAdmin\basisinfo\tables\forms
 */
class ProductRegistered extends Form
{
    public function fields(): array
    {
        return [
            self::input("registered_code", "注册证编号")->col(12)->required(),
            self::select("registered_product_categories", "产品分类")->col(12)->clearable(true)->options(
                self::options()->add('普通耗材', 1)
                    ->add('高值耗材-介入', 2)
                    ->add('高值耗材-外科', 3)
                    ->add('设备及配件', 4)
                    ->add('眼睛类商品', 5)
                    ->add('诊断试剂', 6)
                    ->add('其他产品', 7)->render()
            ),
            self::input("registered_address", "生产地址")->col(12)->required(),
            self::input("registered_name", "注册人名称")->col(12)->required(),
            self::input("registered_company_address", "注册人住所")->col(12)->required(),
            self::input("record_proxy_name", "代理人名称")->col(12)->required(),
            self::input("registered_proxy_address", "代理人住址")->required(),
            self::textarea("comprise_desc", "结构及组成")->required(),
            self::textarea("product_desc", "适用范围")->required(),
            self::date("registered_time", "批准日期")->col(12)->required(),
            self::date("end_time", "有效期至")->col(12)->required(),
            self::input("registered_department", "审批部门"),
            self::input("registered_remark", "备注"),
        ];
    }
}