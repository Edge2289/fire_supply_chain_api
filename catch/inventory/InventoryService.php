<?php
/**
 * Created by PhpStorm.
 * author: xiejiaqing
 * Note: Tired as a dog
 * Date: 2022/1/11
 * Time: 21:32
 */

namespace catchAdmin\inventory;

use catcher\ModuleService;

class InventoryService extends ModuleService
{
    public function loadRouteFrom()
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'route.php';
    }

    public function loadEvents()
    {
        return [
            'attachment' => [],
        ];
    }

    protected function registerCommands()
    {
        $this->commands([]);
    }
}