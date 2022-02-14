<?php
/**
 * Created by PhpStorm.
 * author: xiejiaqing
 * Note: Tired as a dog
 * Date: 2022/2/12
 * Time: 16:38
 */

namespace catchAdmin\permissions\event;


/**
 * 公司创建数据库
 * Class CompanyCreateMysql
 * @package catchAdmin\permissions\event
 */
class CompanyCreateMysql
{
    // 默认库的前缀
    const DATABASE_NAME = "fire";

    private $runQuery = [
        'addDatabase' => "CREATE DATABASE %s",
    ];

    public function handle($params)
    {

    }
}