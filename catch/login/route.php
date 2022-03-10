<?php

/* @var think\Route $router */

$router->group(function () use ($router) {
    # 登入
    $router->get('/', '\catchAdmin\login\controller\Index@index');
    $router->post('login', '\catchAdmin\login\controller\Index@login');
    $router->post('logout', '\catchAdmin\login\controller\Index@logout');
    $router->post('refresh/token', '\catchAdmin\login\controller\Index@refreshToken');
});


