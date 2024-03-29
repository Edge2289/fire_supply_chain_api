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
    // 采购订单 purchase
    $router->group(function () use ($router) {
        $router->get("purchase", "catchAdmin\purchase\controller\PurchaseOrder@index"); // 采购订单列表
        $router->post("purchaseOrder", "catchAdmin\purchase\controller\PurchaseOrder@save"); // 添加采购订单
        $router->put("purchaseOrder/<id>", "catchAdmin\purchase\controller\PurchaseOrder@update"); // 更新采购订单
        $router->post("purchaseOrder/audit/<id>", "catchAdmin\purchase\controller\PurchaseOrder@audit"); // 更新采购订单
        $router->post("purchaseOrder/statement/<id>", "catchAdmin\purchase\controller\PurchaseOrder@statement"); // 结单
        $router->post("purchaseOrder/cancelStatement/<id>", "catchAdmin\purchase\controller\PurchaseOrder@cancelStatement"); // 取消结单
        $router->post("purchaseOrder/invalid/<id>", "catchAdmin\purchase\controller\PurchaseOrder@invalid"); // 作废
    });
    // 入库订单 procurementWare
    $router->group(function () use ($router) {
        $router->get("procurementWare", "catchAdmin\purchase\controller\ProcurementWarehousing@index"); // 入库订单列表
        $router->post("procurementWare", "catchAdmin\purchase\controller\ProcurementWarehousing@save"); // 添加入库订单
        $router->put("procurementWare/<id>", "catchAdmin\purchase\controller\ProcurementWarehousing@save"); // 更新入库订单
        $router->post("procurementWare/audit/<id>", "catchAdmin\purchase\controller\ProcurementWarehousing@audit"); // 更新入库订单
        $router->post("procurementWare/invalid/<id>", "catchAdmin\purchase\controller\ProcurementWarehousing@invalid"); // 作废
    });
})->middleware('auth');

$router->group(function () use ($router) {
    $router->get("receivable/purchaseOrder", "catchAdmin\purchase\controller\PurchaseOrder@getAlertOrder");
    $router->get("purchaseOrder/details", "catchAdmin\purchase\controller\PurchaseOrder@getPurchaseOrderDetails");
});

