<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/1/5
 * Time: 22:47
 */

/* @var think\Route $router */

$router->group(function () use ($router) {
    // 仓库管理
    $router->resource("warehouse", '\catchAdmin\inventory\controller\Warehouse'); // 仓库

    $router->get("inventory", '\catchAdmin\inventory\controller\Inventory@list'); // 库存

    $router->group(function () use ($router) {
        // 寄售出库
        $router->resource('consignmentOutbound', '\catchAdmin\inventory\controller\ConsignmentOutbound');
        $router->post("consignmentOutbound/audit/<id>", '\catchAdmin\inventory\controller\ConsignmentOutbound@audit'); // 审核
        $router->post("consignmentOutbound/invalid/<id>", '\catchAdmin\inventory\controller\ConsignmentOutbound@invalid'); // 作废
        $router->post("consignmentOutbound/turnSales/<id>", '\catchAdmin\inventory\controller\ConsignmentOutbound@turnSales'); // 转销售

    });

    $router->group(function () use ($router) {
        // 备货出库
        $router->resource('readyOutbound', '\catchAdmin\inventory\controller\ReadyOutbound');
        $router->post("readyOutbound/audit/<id>", '\catchAdmin\inventory\controller\ReadyOutbound@audit'); // 审核
        $router->post("readyOutbound/invalid/<id>", '\catchAdmin\inventory\controller\ReadyOutbound@invalid'); // 作废
        $router->post("readyOutbound/turnSales/<id>", '\catchAdmin\inventory\controller\ReadyOutbound@turnSales'); // 转销售
    });

})->middleware('auth');

$router->group(function () use ($router) {
    $router->get("inventoryBatch", '\catchAdmin\inventory\controller\Inventory@inventoryBatchList');
    $router->get("warehouseItem", '\catchAdmin\inventory\controller\Warehouse@tableGetWarehouse');
});