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
 * Class ProductDistributionInfo
 * @package catchAdmin\basisinfo\tables\forms
 */
class ProductDistributionInfo extends Form
{
    public function fields(): array
    {
        $deptData = [
            [
                "dept_id" => 1,
                "dept_name" => "输血科"
            ],
            [
                "dept_id" => 2,
                "dept_name" => "病理科"
            ],
            [
                "dept_id" => 3,
                "dept_name" => "重症医学科"
            ],
            [
                "dept_id" => 4,
                "dept_name" => "超声科"
            ],
            [
                "dept_id" => 5,
                "dept_name" => "放射科"
            ],
            [
                "dept_id" => 6,
                "dept_name" => "医学检验科"
            ],
            [
                "dept_id" => 7,
                "dept_name" => "功能检查科"
            ],
            [
                "dept_id" => 8,
                "dept_name" => "普外科"
            ],
            [
                "dept_id" => 9,
                "dept_name" => "胸外科"
            ],
            [
                "dept_id" => 10,
                "dept_name" => "泌尿外科"
            ],
            [
                "dept_id" => 11,
                "dept_name" => "骨科"
            ],
            [
                "dept_id" => 12,
                "dept_name" => "内科"
            ],
            [
                "dept_id" => 13,
                "dept_name" => "神经内科"
            ],
            [
                "dept_id" => 14,
                "dept_name" => "神经外科"
            ],
            [
                "dept_id" => 15,
                "dept_name" => "消化内科"
            ],
            [
                "dept_id" => 16,
                "dept_name" => "呼吸与危重症医学科"
            ],
            [
                "dept_id" => 17,
                "dept_name" => "肾内科"
            ],
            [
                "dept_id" => 18,
                "dept_name" => "内分泌科"
            ],
            [
                "dept_id" => 19,
                "dept_name" => "肿瘤科"
            ],
            [
                "dept_id" => 20,
                "dept_name" => "耳鼻喉科"
            ],
            [
                "dept_id" => 21,
                "dept_name" => "皮肤科"
            ],
            [
                "dept_id" => 22,
                "dept_name" => "妇产科"
            ],
            [
                "dept_id" => 23,
                "dept_name" => "中医科"
            ],
            [
                "dept_id" => 24,
                "dept_name" => "康复科"
            ],
            [
                "dept_id" => 25,
                "dept_name" => "介入血管科"
            ],
            [
                "dept_id" => 26,
                "dept_name" => "儿童保健科"
            ],
            [
                "dept_id" => 27,
                "dept_name" => "急诊科"
            ],
            [
                "dept_id" => 28,
                "dept_name" => "全选"
            ]
        ];
        $optionRender = self::options();
        foreach ($deptData as $datum) {
            $optionRender->add($datum['dept_name'], $datum['dept_id']);
        }
        return [
            self::image("经销协议图片", "distribution_agreement_url"),
            self::date("signing_date", "签约日期")->col(12),
            self::date("end_time", "有效期")->col(12),
            self::date("payment_days", "账期(天)")->col(12)->required(),
            self::select("transaction_type", "交易类型")->col(12)->clearable(true)->options(
                self::options()->add('普通耗材', 1)
                    ->add('高值耗材-介入', 2)
                    ->add('高值耗材-外科', 3)
                    ->add('设备及配件', 4)
                    ->add('眼睛类商品', 5)
                    ->add('诊断试剂', 6)
                    ->add('其他产品', 7)->render()
            )->required(),
            self::input("admission_lowest_price", "进院最低价(元)")->placeholder("当地进院最低价(元)")->col(12)->required(),
            self::input("guide_price", "当地指导价(元)")->col(12)->required(),
            self::input("provincial_price", "当地省标价(元)")->col(12)->required(),
            self::input("local_price", "当地市标价(元)")->col(12)->required(),
            self::selectMultiple("clinical_use_department", "临床使用科室")->clearable(true)->options(
                $optionRender->render()
            )->style("width:100%")->required(),
            self::input("remark", "备注")->required(),
        ];
    }
}