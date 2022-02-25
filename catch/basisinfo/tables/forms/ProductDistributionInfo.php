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
 * Class ProductDistributionInfo
 * @package catchAdmin\basisinfo\tables\forms
 */
class ProductDistributionInfo extends Form
{
    public function fields(): array
    {
        $optionRender = self::options();
        foreach (ProductDept::data() as $datum) {
            $optionRender->add($datum['dept_name'], $datum['dept_id']);
        }
        return [
            self::image("经销协议图片", "distribution_agreement_url"),
            self::date("signing_date", "签约日期")->col(12),
            self::date("end_time", "有效期")->col(12),
            self::number("payment_days", "账期(天)")->col(12)->required(),
            self::select("transaction_type", "交易类型")->col(12)
                ->style(['width' => '100%'])
                ->allowCreate(true)
                ->filterable(true)
                ->options(
                    self::options()->add('普通耗材', "1")
                        ->add('高值耗材-介入', "2")
                        ->add('高值耗材-外科', "3")
                        ->add('设备及配件', "4")
                        ->add('眼睛类商品', "5")
                        ->add('诊断试剂', "6")
                        ->add('其他产品', "7")->render()
                ),
            self::input("admission_lowest_price", "进院最低价(元)")->placeholder("当地进院最低价(元)")->col(12),
            self::input("guide_price", "当地指导价(元)")->col(12),
            self::input("provincial_price", "当地省标价(元)")->col(12),
            self::input("local_price", "当地市标价(元)")->col(12),
            self::input("remark", "备注"),
            self::selectMultiple("clinical_use_department", "临床使用科室")->clearable(true)->options(
                $optionRender->render()
            )->style("width:100%"),
        ];
    }
}