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
    // 供应商的路由
    $router->group(function () use ($router) {
        $router->get('suppliers', '\catchAdmin\basisinfo\controller\Suppliers@index'); // 列表
        $router->post('suppliers', '\catchAdmin\basisinfo\controller\Suppliers@save'); // 保存
        $router->post('suppliers/uploadAtt/<id>', '\catchAdmin\basisinfo\controller\Suppliers@uploadAtt'); // 更新附件
        $router->put('suppliers/<id>', '\catchAdmin\basisinfo\controller\Suppliers@update'); // 更新
        $router->delete('suppliers/<id>', '\catchAdmin\basisinfo\controller\Suppliers@delete'); // 删除
        $router->post('suppliers/audit/<id>', '\catchAdmin\basisinfo\controller\Suppliers@auditSuppliers'); // 审核供应商
        $router->post('suppliers/open/<id>', '\catchAdmin\basisinfo\controller\Suppliers@openSuppliers'); // 启用供应商
        $router->post('suppliers/disabled/<id>', '\catchAdmin\basisinfo\controller\Suppliers@disabledSuppliers'); // 禁用供应商
    });
    // 厂家路由
    $router->group(function () use ($router) {
        $router->get('factory', '\catchAdmin\basisinfo\controller\Factory@index'); // 厂家
        $router->post('factory', '\catchAdmin\basisinfo\controller\Factory@save'); // 厂家保存
        $router->put('factory/<id>', '\catchAdmin\basisinfo\controller\Factory@update'); // 厂家更新
        $router->post('factory/audit/<id>', '\catchAdmin\basisinfo\controller\Factory@audit'); // 厂家审核

    });
    // 产品路由
    $router->group(function () use ($router) {
        $router->get('product', '\catchAdmin\basisinfo\controller\Product@index'); // 产品列表
        $router->post('product', '\catchAdmin\basisinfo\controller\Product@save'); // 产品保存
        $router->put('product/<id>', '\catchAdmin\basisinfo\controller\Product@update'); // 产品变更
        $router->delete('product/<id>', '\catchAdmin\basisinfo\controller\Product@delete'); // 产品变更
        $router->post('product/audit/<id>', '\catchAdmin\basisinfo\controller\Product@audit'); // 产品审核
    });
    // 客户路由
    $router->group(function () use ($router) {
        $router->get('customer', '\catchAdmin\basisinfo\controller\Customer@index'); // 客户列表
        $router->post('customer', '\catchAdmin\basisinfo\controller\Customer@save'); // 客户保存
        $router->post('customer/uploadAtt/<id>', '\catchAdmin\basisinfo\controller\Customer@uploadAtt'); // 更新附件
        $router->put('customer/<id>', '\catchAdmin\basisinfo\controller\Customer@update'); // 客户变更
        $router->post('customer/audit/<id>', '\catchAdmin\basisinfo\controller\Customer@audit'); // 审核客户
        $router->post('customer/open/<id>', '\catchAdmin\basisinfo\controller\Customer@open'); // 启用客户
        $router->post('customer/disabled/<id>', '\catchAdmin\basisinfo\controller\Customer@disabled'); // 禁用客户
    });

    $router->resource('productCategory', '\catchAdmin\basisinfo\controller\ProductCategory'); // 客户列表
})->middleware('auth');

$router->group(function () use ($router) {
    $router->get('suppliers/changeSuppliersSetting', '\catchAdmin\basisinfo\controller\Suppliers@changeSuppliersSetting');
    $router->get('factory/changeFactorySetting', '\catchAdmin\basisinfo\controller\Factory@changeFactorySetting');
    $router->get('product/changeProductSetting', '\catchAdmin\basisinfo\controller\Product@changeProductSetting');
    $router->post('product/udi', '\catchAdmin\basisinfo\controller\Product@udi');
    $router->get('product/udi', '\catchAdmin\basisinfo\controller\Product@udiList');
    $router->get('product/category', '\catchAdmin\basisinfo\controller\ProductCategory@categoryList');
    $router->get('customer/changeCustomerSetting', '\catchAdmin\basisinfo\controller\Customer@changeCustomerSetting');
    $router->get('product/sku', '\catchAdmin\basisinfo\controller\Product@skuList'); // 产品sku列表
});