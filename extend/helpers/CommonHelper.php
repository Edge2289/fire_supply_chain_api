<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/1/19
 * Time: 17:43
 */


/**
 * 公用的辅助函数
 */

use think\facade\Cache;
use catchAdmin\permissions\model\Users;

if (!function_exists('getCode')) {
    function getCode(string $prefix): string
    {
        return $prefix . date("ymdH") . substr(time(), -1) . rand(10, 99);
    }
}

// 并发数据抢夺
if (!function_exists('is_concurrent')) {
    function is_concurrent(string $cacheKey): bool
    {
        $i = 0;
        while (true) {
            $a = Cache::get($cacheKey);
            if (!$a) {
                break;
            }
            $i++;
            if ($i > 10) {
                return false;
            }
            // 睡眠一秒
            sleep(1);
        }
        return true;
    }
}

if (!function_exists("get_company_employees")) {
    /**
     * 获取公司下的员工
     *
     * @return array
     * @author 1131191695@qq.com
     */
    function get_company_employees(): array
    {
        $userId = request()->user()->id;
        $data = app(Users::class)->where("id", $userId)->find();
        if (!$data['department_id']) {
            return [];
        }
        $data = app(Users::class)->where("department_id", $data['department_id'])->select();
        $map = [];
        foreach ($data as $datum) {
            $map[] = [
                'value' => (string)$datum['id'],
                'label' => $datum['username'],
            ];
        }
        return $map;
    }
}

/**
 * 通过产品id获取注册证号
 *
 * @param int $productId
 * @return string
 * @throws \think\db\exception\DataNotFoundException
 * @throws \think\db\exception\DbException
 * @throws \think\db\exception\ModelNotFoundException
 * @author 1131191695@qq.com
 */
function getProductRegisterCode(int $productId): string
{
    $productBasicInfo = \catchAdmin\basisinfo\model\ProductBasicInfo::where("id", $productId)->find();
    $registered_code = "";
    if (!empty($productBasicInfo)) {
        switch ($productBasicInfo->data_maintenance ?? 3) {
            case 1 :
                $registered_code = $productBasicInfo->withRegistered->registered_code ?? "";
                break;
            case 2 :
                $registered_code = $productBasicInfo->withRecord->record_code ?? "";
                break;
            default :
                $registered_code = "";
                break;
        }
    }
    return $registered_code;
}

/**
 * 获取厂家信息
 *
 * @param int $factoryId
 * @return mixed|string
 * @throws \think\db\exception\DataNotFoundException
 * @throws \think\db\exception\DbException
 * @throws \think\db\exception\ModelNotFoundException
 * @author 1131191695@qq.com
 */
function getFactoryName(int $factoryId)
{
    $factoryModel = \catchAdmin\basisinfo\model\Factory::where("id", $factoryId)->find();
    if (empty($factoryModel)) {
        return "";
    }
    if ($factoryModel->data_maintenance == 1) {
        // 国内厂家
        return $factoryModel->company_name;
    } else {
        // 国外厂家
        return $factoryModel->company_name_en ?: $factoryModel->company_name;
    }
}