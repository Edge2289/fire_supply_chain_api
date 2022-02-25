<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/1/6
 * Time: 10:27
 */

namespace fire\permission;


use catcher\CatchCacheKeys;
use think\facade\Cache;

/**
 * Class ButtonAuth
 * @package fire\permission
 */
trait ButtonAuth
{
    public function getMenuId()
    {
        $permissionIds = Cache::get(CatchCacheKeys::USER_PERMISSIONS . request()->user()->id);
    }
}