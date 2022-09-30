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

/**
 * 获取客户名称
 *
 * @param $customerModel
 * @return mixed|string
 */
function getCustomerName($customerModel)
{
    if ($customerModel == null) {
        return "";
    }
    $company_name = $customerModel['company_name'] ?? "";
    if ($customerModel['customer_type'] == 1) {
        $company_name = $customerModel->hasCustomerLicense["company_name"] ?? '';
    }
    if ($customerModel['customer_type'] == 3) {
        $company_name = $customerModel['hos_name'] ?? "";
    }
    return $company_name;
}

//驼峰命名转下划线命名
function toUnderscore($str)
{
    $dstr = preg_replace_callback('/([A-Z]+)/', function ($matchs) {
        return '_' . strtolower($matchs[0]);
    }, $str);
    return trim(preg_replace('/_{2,}/', '_', $dstr), '_');
}

//下划线命名到驼峰命名
function toCamelCase($str)
{
    $array = explode('_', $str);
    $result = $array[0];
    $len = count($array);
    if ($len > 1) {
        for ($i = 1; $i < $len; $i++) {
            $result .= ucfirst($array[$i]);
        }
    }
    return $result;
}

/**
 * 下划线转驼峰
 * 思路:
 * step1.原字符串转小写,原字符串中的分隔符用空格替换,在字符串开头加上分隔符
 * step2.将字符串中每个单词的首字母转换为大写,再去空格,去字符串首部附加的分隔符.
 */
function camelize($unclaimed_words, $separator = '_'): string
{
    $unclaimed_words = $separator . str_replace($separator, " ", strtolower($unclaimed_words));
    return ltrim(str_replace(" ", "", ucwords($unclaimed_words)), $separator);
}

/**
 * 驼峰命名转下划线命名
 * 思路:
 * 小写和大写紧挨一起的地方,加上分隔符,然后全部转小写
 */
function uncamelize($camelCaps, $separator = '_'): string
{
    return strtolower(preg_replace('/([a-z])([A-Z])/', "$1" . $separator . "$2", $camelCaps));
}
