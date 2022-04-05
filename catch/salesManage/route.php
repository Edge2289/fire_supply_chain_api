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

    // 销售订单
    $router->get("salesOrder", "catchAdmin\salesManage\controller\SalesOrder@index");
    $router->post("salesOrder", "catchAdmin\salesManage\controller\SalesOrder@save");
    $router->put("salesOrder", "catchAdmin\salesManage\controller\SalesOrder@save");
    $router->post("salesOrder/audit/<id>", "catchAdmin\salesManage\controller\SalesOrder@audit");
    $router->post("salesOrder/invalid/<id>", "catchAdmin\salesManage\controller\SalesOrder@invalid");

    // 出库单
    $router->get("outboundOrder", "catchAdmin\salesManage\controller\OutboundOrder@index");
    $router->post("outboundOrder", "catchAdmin\salesManage\controller\OutboundOrder@save");
    $router->put("outboundOrder/<id>", "catchAdmin\salesManage\controller\OutboundOrder@save");
    $router->post("outboundOrder/invalid/<id>", "catchAdmin\salesManage\controller\OutboundOrder@invalid");
    $router->post("outboundOrder/audit/<id>", "catchAdmin\salesManage\controller\OutboundOrder@audit");

})->middleware('auth');

$router->group(function () use ($router) {
    $router->get("salesOrder/outboundOrder", "catchAdmin\salesManage\controller\SalesOrder@outboundOrder");
    $router->get("receivable/outboundOrder", "catchAdmin\salesManage\controller\OutboundOrder@getAlertOrder");
    $router->get("receivable/salesOrder", "catchAdmin\salesManage\controller\SalesOrder@getAlertOrder");
});

