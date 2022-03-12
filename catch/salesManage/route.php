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

// you should use `$router`
/* @var think\Route $router */

$router->group(function () use ($router) {
    $router->get("salesOrder", "catchAdmin\salesManage\controller\SalesOrder@index");
    $router->post("salesOrder", "catchAdmin\salesManage\controller\SalesOrder@save");
    $router->put("salesOrder", "catchAdmin\salesManage\controller\SalesOrder@save");
})->middleware('auth');

