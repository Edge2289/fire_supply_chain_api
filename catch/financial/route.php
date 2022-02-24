<?php
// +----------------------------------------------------------------------
// | CatchAdmin [Just Like ～ ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017~{$year} http://catchadmin.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( https://github.com/yanwenwu/catch-admin/blob/master/LICENSE.txt )
// +----------------------------------------------------------------------
// | Author: JaguarJack [ njphper@gmail.com ]
// +----------------------------------------------------------------------

/* @var think\Route $router */
$router->group(function () use ($router) {
    $router->group(function () use ($router) {
        // 回款单
        $router->get("receivable", '\catchAdmin\financial\controller\Receivable@index'); // 添加
        $router->post("receivable", '\catchAdmin\financial\controller\Receivable@save'); // 提交
        $router->put("receivable", '\catchAdmin\financial\controller\Receivable@update'); // 更新
        $router->delete("receivable", '\catchAdmin\financial\controller\Receivable@delete'); // 删除
    });

    $router->group(function () use ($router) {
        // 收款单
        $router->get("payment", '\catchAdmin\financial\controller\Payment@index'); // 添加
        $router->post("payment", '\catchAdmin\financial\controller\Payment@save'); // 提交
        $router->put("payment", '\catchAdmin\financial\controller\Payment@update'); // 更新
        $router->delete("payment", '\catchAdmin\financial\controller\Payment@delete'); // 删除
    });

    $router->group(function () use ($router) {
        // 发票
        $router->get("invoice", '\catchAdmin\financial\controller\Invoice'); // 添加
        $router->post("invoice", '\catchAdmin\financial\controller\Invoice'); // 提交
        $router->put("invoice", '\catchAdmin\financial\controller\Invoice'); // 更新
        $router->delete("invoice", '\catchAdmin\financial\controller\Invoice'); // 删除
    });
})->middleware('auth');

$router->group(function () use ($router) {

});