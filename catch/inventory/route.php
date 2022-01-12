<?php
/**
 * Created by PhpStorm.
 * author: xiejiaqing
 * Note: Tired as a dog
 * Date: 2022/1/5
 * Time: 22:47
 */

/* @var think\Route $router */

$router->group(function () use ($router) {
    // 仓库管理
    $router->resource("warehouse", '\catchAdmin\inventory\controller\Warehouse'); // 仓库
})->middleware('auth');