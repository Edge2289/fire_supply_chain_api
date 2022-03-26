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