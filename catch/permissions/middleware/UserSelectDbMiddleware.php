<?php
/**
 * Created by PhpStorm.
 * author: xiejiaqing
 * Note: Tired as a dog
 * Date: 2022/1/5
 * Time: 21:34
 */

namespace catchAdmin\permissions\middleware;

use app\Request;

/**
 * 根据登陆用户获取需要链接的数据库
 *
 * Class UserSelectDb
 * @package catchAdmin\permissions\middleware
 */
class UserSelectDbMiddleware
{
    public function handle(Request $request, \Closure $next)
    {
        $database = app()->config->get("database");
        $database['connections']['business']['database'] = "fire_1";
        app()->config->set($database, "database");
        return $next($request);
    }
}