<?php
/**
 * Created by PhpStorm.
 * author: xiejiaqing
 * Note: Tired as a dog
 * Date: 2022/1/7
 * Time: 17:16
 */

use think\migration\Seeder;

/**
 * Class EquipmentClassSeeds
 * @package catchAdmin\basisinfo\database\seeds
 */
class EquipmentClassSeeds extends Seeder
{
    public function run()
    {
        \think\facade\Db::name( 'equipment_class')->insertAll($this->getPermissions());
    }

    protected function getPermissions()
    {
        $s2 = "01 - 有源手术器械,02 - 无源手术器械,03 - 神经和心血管手术器械,04 - 骨科手术器械,05 - 放射治疗器械,06 - 医用成像器械,07 - 医用诊察和监护器械,08 - 呼吸、麻醉和急救器械,09 - 物理治疗器械,10 - 输血、透析和体外循环器械,11 - 医疗器械消毒灭菌器械,12 - 有源植入器械,13 - 无源植入器械,14 - 注输、护理和防护器械,15 - 患者承载器械,16 - 眼科器械,17 - 口腔科器械,18 - 妇产科、辅助生殖和避孕器械,19 - 医用康复器械,20 - 中医器械,21 - 医用软件,22 - 临床检验器械";
        $s22 ="6821、6816、6822、6823、6824、6825、6854、6858,6801、6802、6805、6808、6809、6816、6822、6865,6803、6807、6877,6810、6826,6830、6832、6833,6821、6822、6823、6824、6828、6830、6831、6833、6834,6820、6821、6823,6805、6821、6823、6826、6854、6856、6866,6821、6823、6824、6825、6826、6854、6856、6866,6845、6866,6857,6821、6846,6846、6877,6815、6854、6856、6864、6866,6854、6856,6804、6820、6822、6823、6824、6846、6858,6806、6823、6855、6863,6812、6813、6822、6823、6826、6846、6854、6865、6866,6826、6846,6827,6870,6815、6822、6833、6840、6841、6858、6840";

        $s2Map = explode(",", $s2);
        $s22Map = explode(",", $s22);
        $map = [];
        foreach ($s2Map as $key => $value) {
            $map[] =
                [
                    'scope2017' => $value,
                    'scope2002' => $s22Map[$key] ?? "",
                    'created_at' => time(),
                    'updated_at' => time(),
                    'deleted_at' => 0,
                ];
        }
        return $map;
    }
}