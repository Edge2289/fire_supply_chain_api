<?php
declare(strict_types=1);

namespace catcher\base;

use catcher\exceptions\BusinessException;

abstract class CatchController
{
    /**
     * 魔术方法
     *
     * @param $method
     * @param mixed $param
     * @return mixed
     * @author 1131191695@qq.com
     */
    public final function __call($method, $param)
    {
        $methods = explode("_", $method);
        if (empty($methods)) {
            throw new BusinessException("缺失方法");
        }
        $m = "";
        foreach ($methods as $k => $method) {
            if ($k) {
                $method = ucwords($method);
            }
            $m .= $method;
        }
        $m .= "Call";
        if (!method_exists($this, $m)) {
            throw new BusinessException(sprintf("缺失方法不存在[%s]", $m));
        }
        return $this->{$m}(...$param);
    }

    /**
     *
     *
     * @param string $class
     * @param array $params
     * @author 1131191695@qq.com
     */
    public final function validator(string $class, array $params)
    {
        new $class($params);
    }
}
