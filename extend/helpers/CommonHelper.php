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
if (!function_exists('getCode')) {
    function getCode(string $prefix): string
    {
        return $prefix . date("ymdH") . substr(time(), -1) . rand(10, 99);
    }
}
