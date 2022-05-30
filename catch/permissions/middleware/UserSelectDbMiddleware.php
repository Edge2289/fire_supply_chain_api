<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
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
    /**
     * @var string
     */
    private $databasePrefix = "fire";

    public function handle(Request $request, \Closure $next)
    {
        // 根据公司的id去获取库的名称
        // 一般来说，公司id为1 数据库名称为 fire_1
        $database = app()->config->get("database");
        $database['connections']['business']['database'] = $this->getDatabaseName($request);
        app()->config->set($database, "database");
        return $next($request);
    }

    /**
     * 获取数据库名称
     *
     * @param Request $request
     * @return string
     * @author 1131191695@qq.com
     */
    private function getDatabaseName(Request $request): string
    {
        if ($request->user()['company_id']) {
            return $this->databasePrefix . "_" . ((int)$request->user()['company_id']);
        }
        return $this->databasePrefix;
    }
}