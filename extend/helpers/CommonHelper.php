<?php
/**
 * Created by PhpStorm.
 * author: xiejiaqing
 * Note: Tired as a dog
 * Date: 2022/1/19
 * Time: 17:43
 */


/**
 * 公用的辅助函数
 */
if (!function_exists('getCode')) {
    function getCode(string $prefix): string {
        return $prefix . date("ymdHi") . substr(time(), -1) . rand(100, 999) . rand(100, 999);
    }
}
