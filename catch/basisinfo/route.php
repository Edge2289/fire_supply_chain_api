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
    # 登入
    $router->get('upload/img', '\catchAdmin\basisinfo\controller\UploadFile@uploadImg');
});