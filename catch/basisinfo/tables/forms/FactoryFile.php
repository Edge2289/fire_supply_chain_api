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
use catchAdmin\basisinfo\model\Factory;

/**
 * Class FactoryFile
 * @package catchAdmin\basisinfo\tables\forms
 */
class FactoryFile extends Form
{
    public function fields(): array
    {
        $id = $_GET['id'];
        $data = app(Factory::class)->findBy($id);
        $imageData = [
            "business_license_url" => "营业执照",
            "contract_url" => "上传合同",
            "production_license_url" => "生产许可证",
            "record_license_url" => "备案凭证照片",
        ];
        if ($data->data_maintenance == 1) {
            unset($imageData["record_license_url"]);
        } else if ($data->data_maintenance == 2) {
            unset($imageData["production_license_url"]);
        } else {
            unset($imageData["record_license_url"], $imageData["production_license_url"]);
        }

        $assemblyImageData = [
            self::hidden("business_license_id", 0)
        ];
        $assemblyCheckboxData = [];
        foreach ($imageData as $k => $v) {
            $that = self::image($v, $k)->style('label-width="140px"')->col(12);
            if ($k != 'contract_url') {
                $that = $that->required();
            }
            $assemblyImageData[] = $that;
        }
        return array_merge($assemblyImageData, $assemblyCheckboxData);
    }
}