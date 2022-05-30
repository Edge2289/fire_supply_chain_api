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
    // customer 接口
    $router->get('sccs/changeCustomerSetting', '\catchAdmin\sccs\controller\Customer@changeCustomerSetting');
    $router->post('sccs/customer', '\catchAdmin\sccs\controller\Customer@save');
    $router->put('sccs/customer', '\catchAdmin\sccs\controller\Customer@save');

    // salesOrder
    $router->get("sccs/salesOrder", "catchAdmin\sccs\controller\SalesOrder@index");
    $router->post("sccs/salesOrder", "catchAdmin\sccs\controller\SalesOrder@save");
    $router->put("sccs/salesOrder/<id>", "catchAdmin\sccs\controller\SalesOrder@save");
})->middleware('auth');


$router->post("sccs/salesOrder/invalid/<id>", "catchAdmin\sccs\controller\SalesOrder@invalid");