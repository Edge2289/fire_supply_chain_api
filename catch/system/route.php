<?php

/* @var think\Route $router */

$router->group(function () use ($router) {
    // 登录日志
    $router->get('log/login', '\catchAdmin\system\controller\LoginLog@list');
    $router->delete('log/login/<id>', '\catchAdmin\system\controller\LoginLog@empty');
    // 操作日志
    $router->get('log/operate', '\catchAdmin\system\controller\OperateLog@list');
    // $router->delete('empty/log/operate', '\catchAdmin\system\controller\OperateLog@empty');
    $router->delete('log/operate/<id>', '\catchAdmin\system\controller\OperateLog@delete');

    // 数据字典
    $router->get('tables', '\catchAdmin\system\controller\DataDictionary@tables');
    $router->get('table/view/<table>', '\catchAdmin\system\controller\DataDictionary@view');
    $router->post('table/optimize', '\catchAdmin\system\controller\DataDictionary@optimize');
    $router->post('table/backup', '\catchAdmin\system\controller\DataDictionary@backup');

    // 上传
    $router->group('upload', function () use ($router) {
        $router->post('image', '\catchAdmin\system\controller\Upload@image');
        $router->post('file', '\catchAdmin\system\controller\Upload@file');
    })->middleware(\catcher\middlewares\JsonResponseMiddleware::class);

    // 附件
    $router->resource('attachments', '\catchAdmin\system\controller\Attachments');

    // 配置
    $router->get('config/parent', '\catchAdmin\system\controller\Config@parent');
    $router->resource('config', '\catchAdmin\system\controller\Config');

    // 代码生成
    $router->post('generate', '\catchAdmin\system\controller\Generate@save');
    $router->post('generate/preview', '\catchAdmin\system\controller\Generate@preview'); // 预览
    $router->post('generate/create/module', '\catchAdmin\system\controller\Generate@createModule'); // 创建模块

    //developer路由
    $router->resource('developer', '\catchAdmin\system\controller\Developer')->middleware('auth');
    // excel 导入&导出通用
    $router->post('excel/export', '\catchAdmin\system\controller\Excel@export');
    $router->post('excel/import', '\catchAdmin\system\controller\Excel@import');

})->middleware('auth');

// 获取 table
$router->get('table/<module>/<tableClass>', function ($module, $tableClass) {
    $table = sprintf('\\catchAdmin\\%s\\tables\\%s', $module, ucfirst($tableClass));

    return (new $table)->render(request()->param('only'));
});
$router->get('config/getSeparateConfig/<parent>/<key>', '\catchAdmin\system\controller\Config@getSeparateConfig');

