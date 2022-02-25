<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/1/9
 * Time: 20:59
 */

namespace catchAdmin\basisinfo\tables\forms;


use catcher\library\form\Form;
use FormBuilder\Factory\Elm;

/**
 * Class QualificationFile
 * @package catchAdmin\basisinfo\tables\forms
 */
class QualificationFile extends Form
{
    public function fields(): array
    {
        $imageData = [
            "business_license_url" => "营业执照",
            "production_license_url" => "医疗器械经营许可证/生产许可证",
            "record_certificate_url" => "第二类医疗器械经营备案凭证/生产备案凭证",
            "invoice_information_url" => "开票资料",
            "person_authorization_url" => "法人委托授权书",
            "assurance_agreement_url" => "质量保证协议书",
            "delivery_template_url" => "出库单模板",
            "seal_filing_template_url" => "印章备案模板",
            "system_survey_form_url" => "质量体系调查表",
            "annual_report_url" => "年度报告",
        ];
        $assemblyImageData = [
            self::hidden("business_license_id", 0)
        ];
        $assemblyCheckboxData = [];
        $i = 0;
        foreach ($imageData as $k => $v) {
            $that = self::image($v, $k)->style('label-width="140px"')->col(12);
            if ($i < 5) {
                $that = $that->required();
            }
            $i++;
            $assemblyImageData[] = $that;
            $spilt = explode("_", $k);
            unset($spilt[count($spilt) - 1]);
            $assemblyCheckboxData[] = self::checkbox("check_" . implode("_", $spilt), '')
                ->options(function () use ($k, $v) {
                    return [Elm::option(1, $v . "（复印件 / 原件）已核对，验收合格")];
                })
                ->col(12);
        }
        return array_merge($assemblyImageData, $assemblyCheckboxData);
    }
}