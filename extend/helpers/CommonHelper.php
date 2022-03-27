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