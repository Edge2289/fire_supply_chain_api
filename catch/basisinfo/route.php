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
    $router->get('suppliers', '\catchAdmin\basisinfo\controller\Suppliers@index');
    $router->post('suppliers', '\catchAdmin\basisinfo\controller\Suppliers@save');
    $router->put('suppliers/<id>', '\catchAdmin\basisinfo\controller\Suppliers@update');
    $router->delete('suppliers/<id>', '\catchAdmin\basisinfo\controller\Suppliers@delete');
})->middleware('auth');

$router->group(function () use ($router){
    $router->get('suppliers/changeSuppliersSetting', '\catchAdmin\basisinfo\controller\Suppliers@changeSuppliersSetting');
});