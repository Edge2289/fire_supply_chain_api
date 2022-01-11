<?php
/**
 * Created by PhpStorm.
 * author: xiejiaqing
 * Note: Tired as a dog
 * Date: 2022/1/5
 * Time: 22:47
 */

/* @var think\Route $router */

$router->group(function () use ($router){
    // 供应商的路由
    $router->group(function () use ($router) {
        $router->get('suppliers', '\catchAdmin\basisinfo\controller\Suppliers@index'); // 列表
        $router->post('suppliers', '\catchAdmin\basisinfo\controller\Suppliers@save'); // 保存
        $router->put('suppliers/<id>', '\catchAdmin\basisinfo\controller\Suppliers@update'); // 更新
        $router->delete('suppliers/<id>', '\catchAdmin\basisinfo\controller\Suppliers@delete'); // 删除
        $router->post('suppliers/audio/<id>', '\catchAdmin\basisinfo\controller\Suppliers@auditSuppliers'); // 审核供应商
        $router->post('suppliers/open/<id>', '\catchAdmin\basisinfo\controller\Suppliers@openSuppliers'); // 启用供应商
        $router->post('suppliers/disabled/<id>', '\catchAdmin\basisinfo\controller\Suppliers@disabledSuppliers'); // 禁用供应商
    });
    // 厂家路由
    // 购货者路由
    // 仓库路由
    // 产品路由

})->middleware('auth');

$router->group(function () use ($router){
    $router->get('suppliers/changeSuppliersSetting', '\catchAdmin\basisinfo\controller\Suppliers@changeSuppliersSetting');
});