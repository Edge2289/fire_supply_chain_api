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

$router->group(function () use ($router){
    $router->group(function() use ($router) {
        $router->get("purchase", "catchAdmin\purchase\controller\PurchaseOrder@index"); // 采购订单列表
    });
})->middleware('auth');

