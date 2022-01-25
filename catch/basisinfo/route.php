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
    $router->group(function () use ($router) {
        $router->get('factory', '\catchAdmin\basisinfo\controller\Factory@index'); // 厂家
        $router->post('factory', '\catchAdmin\basisinfo\controller\Factory@save'); // 厂家保存
        $router->put('factory/<id>', '\catchAdmin\basisinfo\controller\Factory@update'); // 厂家更新
        $router->post('factory/audio/<id>', '\catchAdmin\basisinfo\controller\Factory@update'); // 厂家审核

    });
    // 购货者路由
    // 产品路由
    $router->group(function () use ($router) {
        $router->get('product', '\catchAdmin\basisinfo\controller\Product@index'); // 厂家
        $router->post('product', '\catchAdmin\basisinfo\controller\Product@save'); // 厂家
        $router->put('product/<id>', '\catchAdmin\basisinfo\controller\Product@update'); // 厂家
        $router->post('product/audio/<id>', '\catchAdmin\basisinfo\controller\Product@update'); // 厂家

    });

})->middleware('auth');

$router->group(function () use ($router){
    $router->get('suppliers/changeSuppliersSetting', '\catchAdmin\basisinfo\controller\Suppliers@changeSuppliersSetting');
    $router->get('factory/changeFactorySetting', '\catchAdmin\basisinfo\controller\Factory@changeFactorySetting');
    $router->get('product/changeProductSetting', '\catchAdmin\basisinfo\controller\Product@changeProductSetting');
});