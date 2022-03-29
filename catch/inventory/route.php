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
    });

    $router->group(function () use ($router) {
        // 备货出库
        $router->resource('readyOutbound', '\catchAdmin\inventory\controller\ReadyOutbound');
    });

})->middleware('auth');

$router->group(function () use ($router) {
    $router->get("inventoryBatch", '\catchAdmin\inventory\controller\Inventory@inventoryBatchList');
});