<?php
// +----------------------------------------------------------------------
// | CatchAdmin [Just Like ～ ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017~2020 http://catchadmin.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( https://github.com/yanwenwu/catch-admin/blob/master/LICENSE.txt )
// +----------------------------------------------------------------------
// | Author: JaguarJack [ njphper@gmail.com ]
// +----------------------------------------------------------------------
namespace catchAdmin\basisinfo;

use catcher\ModuleService;

class BasisInfoService extends ModuleService
{

    public function loadRouteFrom()
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'route.php';
    }

    public function loadEvents()
    {
        return [
            'attachment' => [ ],
        ];
    }

    protected function registerCommands()
    {
        $this->commands([
        ]);
    }
}